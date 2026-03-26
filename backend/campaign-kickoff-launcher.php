<?php
/**
 * Campaign Kickoff Launcher
 *
 * Responsible for:
 * - batching leads
 * - orchestrating worker calls
 * - handling retries and concurrency
 * - aggregating results
 * - sending execution summary
 */

header('Content-Type: application/json');

/**
 * ============================
 * CONFIGURATION (NO SECRETS)
 * ============================
 */

$token = getenv('CAMPAIGN_TOKEN');
$tag = 'React Campaign';

$maxCalls = 100;
$batchSize = 10;
$concurrency = 3;

$workerBase = getenv('WORKER_KICKOFF_URL');
$discordWebhook = getenv('DISCORD_WEBHOOK_URL');

$connectTimeout = 12;
$requestTimeout = 120;

$maxRetriesPerPage = 2;
$retryDelaySeconds = 2;

/**
 * ============================
 * HELPER FUNCTIONS
 * ============================
 */

function buildWorkerUrl($workerBase, $token, $tag, $maxCalls, $batchSize, $page) {
    return $workerBase .
        '?token=' . urlencode($token) .
        '&tag=' . urlencode($tag) .
        '&maxCalls=' . urlencode($maxCalls) .
        '&batchSize=' . urlencode($batchSize) .
        '&page=' . urlencode($page);
}

function createCurlHandle($url, $connectTimeout, $requestTimeout) {
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => $connectTimeout,
        CURLOPT_TIMEOUT => $requestTimeout,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_USERAGENT => 'OutreachEngine-Kickoff/1.0',
    ]);

    return $ch;
}

function normalizeCurlResult($ch, $body) {
    return [
        'httpCode' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        'error' => curl_error($ch) ?: null,
        'errno' => curl_errno($ch) ?: 0,
        'rawBody' => $body,
        'decodedBody' => json_decode($body, true),
    ];
}

function isSuccessfulResponse($response) {
    return (
        $response['httpCode'] >= 200 &&
        $response['httpCode'] < 300 &&
        empty($response['error'])
    );
}

function sendDiscordMessage($webhookUrl, $message) {
    if (empty($webhookUrl)) {
        return ['ok' => false, 'error' => 'Missing webhook'];
    }

    $payload = json_encode(['content' => $message]);

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $webhookUrl,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $body = curl_exec($ch);

    $result = [
        'httpCode' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
        'error' => curl_error($ch) ?: null,
        'errno' => curl_errno($ch) ?: 0,
        'body' => $body,
    ];

    curl_close($ch);

    $result['ok'] = (
        $result['httpCode'] >= 200 &&
        $result['httpCode'] < 300 &&
        empty($result['error'])
    );

    return $result;
}

/**
 * ============================
 * BUILD PAGES (BATCHES)
 * ============================
 */

$totalPages = (int) ceil($maxCalls / $batchSize);
$pages = [];

for ($page = 1; $page <= $totalPages; $page++) {
    $pages[$page] = [
        'page' => $page,
        'url' => buildWorkerUrl($workerBase, $token, $tag, $maxCalls, $batchSize, $page),
        'attemptCount' => 0,
        'attempts' => [],
        'success' => false,
        'done' => false,
        'final' => null,
    ];
}

/**
 * ============================
 * EXECUTION LOOP
 * ============================
 */

for ($attemptRound = 1; $attemptRound <= $maxRetriesPerPage; $attemptRound++) {

    $pendingPages = array_filter($pages, fn($p) => !$p['done']);
    if (empty($pendingPages)) break;

    $chunks = array_chunk(array_keys($pendingPages), $concurrency);

    foreach ($chunks as $chunk) {
        $mh = curl_multi_init();
        $handles = [];

        foreach ($chunk as $pageNum) {
            $pages[$pageNum]['attemptCount']++;

            $ch = createCurlHandle(
                $pages[$pageNum]['url'],
                $connectTimeout,
                $requestTimeout
            );

            curl_multi_add_handle($mh, $ch);
            $handles[$pageNum] = $ch;
        }

        do {
            $status = curl_multi_exec($mh, $running);
            if ($running) curl_multi_select($mh, 1.0);
        } while ($running && $status == CURLM_OK);

        foreach ($handles as $pageNum => $ch) {
            $body = curl_multi_getcontent($ch);
            $response = normalizeCurlResult($ch, $body);

            $attemptLog = [
                'attempt' => $pages[$pageNum]['attemptCount'],
                'httpCode' => $response['httpCode'],
                'error' => $response['error'],
                'response' => $response['decodedBody'] ?? $response['rawBody'],
            ];

            $pages[$pageNum]['attempts'][] = $attemptLog;
            $pages[$pageNum]['final'] = $attemptLog;

            if (isSuccessfulResponse($response)) {
                $pages[$pageNum]['success'] = true;
                $pages[$pageNum]['done'] = true;
            }

            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }

        curl_multi_close($mh);
        usleep(300000);
    }

    sleep($retryDelaySeconds);
}

/**
 * ============================
 * FINALIZE RESULTS
 * ============================
 */

$successCount = 0;
$failedPages = [];

foreach ($pages as $pageNum => $pageData) {
    if ($pageData['success']) {
        $successCount++;
    } else {
        $failedPages[] = $pageNum;
    }
}

$estimatedProcessed = min($successCount * $batchSize, $maxCalls);

/**
 * ============================
 * SEND SUMMARY
 * ============================
 */

$summaryMessage =
    "Kickoff Campaign Summary\n" .
    "Tag: {$tag}\n" .
    "Leads Requested: {$maxCalls}\n" .
    "Batch Size: {$batchSize}\n" .
    "Successful Pages: {$successCount}\n" .
    "Failed Pages: " . count($failedPages) . "\n" .
    "Processed: {$estimatedProcessed}";

$discordResult = sendDiscordMessage($discordWebhook, $summaryMessage);

/**
 * ============================
 * OUTPUT
 * ============================
 */

echo json_encode([
    'ok' => true,
    'summary' => [
        'successfulPages' => $successCount,
        'failedPages' => $failedPages,
        'processed' => $estimatedProcessed,
    ],
    'discord' => $discordResult,
], JSON_PRETTY_PRINT);
