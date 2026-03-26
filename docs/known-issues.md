# Known Issues

## 1. Duplicate Messages and Voicemails

**Status:** Open  
**Severity:** High  

**Description:**  
Some leads received duplicate SMS messages and duplicate voicemail drops during campaign execution.

**Impact:**  
This creates a poor lead experience and may reduce trust in campaign delivery.

**Next Step:**  
Review trigger logic, duplicate execution conditions, and worker control.

---

## 2. React Attempt Count Not Updating

**Status:** Open  
**Severity:** Medium  

**Description:**  
The react attempt custom field did not update during follow-up execution.

**Impact:**  
Follow-up tracking becomes incomplete and campaign logic may become unreliable.

**Next Step:**  
Review field update logic inside the follow-up execution flow.

---

## 3. Logging Order Appears Reversed

**Status:** Open  
**Severity:** Medium  

**Description:**  
Nutshell logs text activity before voicemail activity, although expected sequence is voicemail first and then SMS.

**Impact:**  
It is unclear whether the issue affects only logging or actual message delivery order.

**Next Step:**  
Validate real delivery order during the next run.

---

## 4. System Performance Threshold Around 90 Leads

**Status:** Monitoring  
**Severity:** Medium  

**Description:**  
The follow-up campaign performed successfully below approximately 90 leads. Larger volume behavior still needs confirmation.

**Impact:**  
System scalability may currently be limited.

**Next Step:**  
Observe the next campaign run and compare behavior under similar and larger volumes.
