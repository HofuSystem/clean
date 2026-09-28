# LOCAL BENCHMARK DATA CLEANUP — DRY-RUN AUDIT REPORT
**Document Reference:** `docs/notifications/LOCAL_BENCHMARK_DATA_CLEANUP_DRY_RUN.md`  
**Execution Date:** 2026-09-28  
**Mode:** DRY-RUN ONLY (ZERO DELETIONS EXECUTED)  
**Target Database:** Local Development MySQL (`CleanStation`)  

---

## 1. Overview & Deletion Policy
In accordance with Audit Rule #8 and Section 11 ("Document synthetic records in local database without deleting or modifying them"), this dry-run report documents the exact scope of synthetic records inserted into the primary MySQL database (`CleanStation`) during prior unisolated executions of `scratch/benchmark_performance.php`.

**STRICT COMPLIANCE NOTICE:**
- **Zero DELETE queries** were executed.
- **Zero TRUNCATE or fresh migrations** were executed.
- All 1,050 synthetic records remain intact in the local database.
- These synthetic records affect local dashboard aggregates but have **zero impact on Production**.

---

## 2. Identified Synthetic Records Breakdown

### A. Table: `notification_tokens`
- **Total Synthetic Records:** `1,050`
- **ID Range:** Min ID = `7604`, Max ID = `8653`
- **Associated Notification Campaign IDs:** `1117767` and `1117768`
- **Created Timestamp Range:** `2026-09-28 10:35:23` to `2026-09-28 10:36:40`
- **Status Distribution:**
  - `accepted`: `1,050` rows (100%)
- **Token Pattern:** `bench_token_*`

### B. Table: `notifications`
- **Total Synthetic Campaigns:** `2`
- **Campaign Details:**
  1. **ID `1117767`:**
     - Title: `Benchmark Campaign`
     - Status: `processing`
     - Created At: `2026-09-28 10:35:23`
     - Types: `["apps"]`
     - Targeted Count: `50`
  2. **ID `1117768`:**
     - Title: `Benchmark Campaign`
     - Status: `processing`
     - Created At: `2026-09-28 10:36:22`
     - Types: `["apps"]`
     - Targeted Count: `1000`

### C. Tables: `users` & `devices`
- **Synthetic Users:** `0` (confirmed: no users with email like `bench%` exist in MySQL).
- **Synthetic Devices:** `0` (confirmed: no device records were created; synthetic benchmark tokens were passed directly into queue batches).

---

## 3. Proof of Origin
The records were produced by `scratch/benchmark_performance.php` run at `10:35` and `10:36` on 2026-09-28 before automated safety gates were established in `tests/TestCase.php`. The script bootstrapped the Laravel application without forcing the `testing` environment, defaulting to the `.env` default connection (`mysql` on `CleanStation`).

---

## 4. Future Proposed Deletion Scope (If Approved)
When management approves cleaning the local development database, the exact safe deletion commands will be:

```sql
-- DRY RUN SPECIFICATION — DO NOT RUN WITHOUT EXPLICIT MANAGEMENT APPROVAL
START TRANSACTION;

-- 1. Delete synthetic token records
DELETE FROM notification_tokens 
WHERE notification_id IN (1117767, 1117768)
  AND id BETWEEN 7604 AND 8653;

-- 2. Delete synthetic campaign records
DELETE FROM notifications 
WHERE id IN (1117767, 1117768)
  AND title = 'Benchmark Campaign';

-- Verify counts before commit:
-- Expected rows affected: 1,050 in notification_tokens, 2 in notifications.

COMMIT;
```

---

## 5. Production Impact Statement
- **Production Contamination:** **NONE** (No connection to Production exists).
- **Local Application Integrity:** All real local users, orders, and legacy notification records remain unaltered.
- **Local Dashboard:** Until cleaned, local dashboard totals reflect +1,050 accepted push notifications.
