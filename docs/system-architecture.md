# Outreach Engine System Architecture

## Purpose

The Outreach Engine is an automated prospecting and follow-up system designed to contact leads, log activity, track responses, and support campaign execution at scale.

Its goal is to reduce manual outreach work while maintaining visibility, structure, and control across the campaign lifecycle.

---

## Core Components

### 1. Lead Source

Leads are stored and managed inside Nutshell CRM.

These leads are grouped, tagged, filtered, and prepared before entering a campaign.

---

### 2. Campaign Trigger Layer

Campaigns are launched manually based on the selected workflow.

Current campaign structure includes:

- Kickoff Campaign
- Follow-Up Campaign

Each campaign has its own logic, timing, and message assets.

---

### 3. Execution Engine

The execution layer runs through Bluehost using custom backend logic.

This layer is responsible for:

- reading lead data
- triggering voicemail drops
- sending SMS messages
- controlling sequencing
- updating campaign-related fields
- handling campaign flow rules

---

### 4. Communication Layer

Twilio handles outbound communication.

This includes:

- voicemail delivery
- SMS delivery
- inbound message handling

Twilio is used as the communications provider, but platform limitations prevented the original campaign from scaling as intended directly inside Twilio alone.

---

### 5. CRM Logging Layer

Nutshell CRM stores campaign activity and lead-level updates.

This includes:

- text message activity
- voicemail activity
- reply tracking
- unsubscribe tracking
- custom field updates
- date-based activity history

---

### 6. Reply and Exception Layer

When leads respond, unsubscribe, or trigger exceptions, the system records those events for follow-up handling and campaign control.

This layer is critical for:

- stopping unnecessary follow-ups
- identifying engaged leads
- preventing repeated contact
- improving campaign logic over time

---

## Current Campaign Flow

### Kickoff Campaign Flow

1. Lead selected from Nutshell
2. Campaign launched
3. Voicemail drop triggered
4. SMS sent
5. Email sent
6. Activity logged in Nutshell
7. Lead waits for reply, unsubscribe, or next campaign step

---

### Follow-Up Campaign Flow

1. Lead selected based on previous campaign status
2. Follow-up campaign launched
3. Voicemail drop triggered
4. SMS sent
5. Activity logged in Nutshell
6. Custom fields updated
7. Lead monitored for reply, unsubscribe, or next action

---

## Known System Constraints

### 1. Volume Sensitivity

The system appears to perform more reliably when campaign volume remains below approximately 90 leads.

### 2. Field Update Inconsistency

The react attempt count did not update correctly during follow-up execution, although the date field updated successfully.

### 3. Duplicate Execution Risk

Some leads received duplicate voicemail and SMS actions, indicating a need for tighter trigger control.

### 4. Logging Sequence Ambiguity

Nutshell currently logs SMS before voicemail, although expected execution order is voicemail first and then SMS.

It is not yet confirmed whether this is only a logging issue or a true delivery order issue.

---

## Operating Principle

This system is being improved through controlled iteration.

The current approach is:

- test
- observe
- document
- validate
- refine

The goal is not first-run perfection.

The goal is stable and scalable execution.

---

## Status

Current Phase: Active testing and refinement

Next Focus:

- validate second run behavior
- confirm duplication pattern
- review follow-up field update logic
- confirm actual delivery sequence
- implement fixes after observed patterns are confirmed
