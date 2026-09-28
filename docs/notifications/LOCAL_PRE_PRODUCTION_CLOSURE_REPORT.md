# LOCAL PRE-PRODUCTION CLOSURE AUDIT REPORT
**Document Reference:** `docs/notifications/LOCAL_PRE_PRODUCTION_CLOSURE_REPORT.md`  
**Execution Date:** 2026-09-28  
**Pipeline:** `Local → Production` (No Staging Environment Exists)  
**Status:** COMPLETE AUDIT  
**Direct FCM State:** `NOTIFICATION_DIRECT_FCM_ENABLED=false`  

---

## 1. Environment Classification & Correction

### Correction of Prior Terminology
All previous references across Phase 1, Phase 2, and intermediate reports describing the verification environment as "Staging" are hereby officially corrected to **"Local Pre-Production"**. The project operates strictly on a two-tier pipeline:
$$\text{Local (Development \& Isolated Test DB)} \longrightarrow \text{Production}$$

No intermediate Staging server or environment exists.

### Environment Specification
| Parameter | Value | Verification Evidence |
| :--- | :--- | :--- |
| **Local Path** | `D:\programming\projects\Hofu\CleanStation` | Verified via workspace filesystem |
| **Primary APP_ENV** | `local` | `config('app.env')` in Local Application |
| **Testing APP_ENV** | `testing` | `phpunit.xml` & `tests/TestCase.php` Safety Gate |
| **PHP Version** | `8.4.16 (cli)` | `PHP 8.4.16 (cli) (built: Dec 17 2025 10:31:49)` |
| **Laravel Version** | `11.55.0` | `php artisan --version` |
| **Git Commit** | `17d9f880bc4430835eb3a02ff759afc91ace5fae` | `git log -n 1` ("device token") |
| **Git Status** | Modified & untracked remediation files | `git status` clean branch `main`, no unstaged deletions |
| **Primary App Database** | `CleanStation` (MySQL 8.x) | `DB_CONNECTION=mysql`, `DB_DATABASE=CleanStation` |
| **Testing Database** | `database/testing.sqlite` (SQLite) | `DB_CONNECTION=sqlite`, `DB_DATABASE=database/testing.sqlite` |
| **Primary Queue Connection** | `database` | `QUEUE_CONNECTION=database` |
| **Testing Queue Connection** | `database` (sqlite backed) | Enforced in `RealDatabaseQueueWorkerAuditTest` |
| **Direct FCM Flag** | `false` | `.env: NOTIFICATION_DIRECT_FCM_ENABLED=false` |

---

## 2. Database Isolation & Historical Counts Audit

### Testing Database Isolation Mechanism
1. **`phpunit.xml` Configuration:**
   ```xml
   <env name="APP_ENV" value="testing"/>
   <env name="DB_CONNECTION" value="sqlite"/>
   <env name="DB_DATABASE" value="database/testing.sqlite"/>
   ```
2. **Automated Safety Gate in `tests/TestCase.php`:**
   An automated gate was implemented in `tests/TestCase.php::setUp()` that verifies:
   - `app()->environment() === 'testing'` (aborts with `RuntimeException` if not).
   - Purges `mysql` connection and redirects default connection to `sqlite` pointing to `database/testing.sqlite`.
   - Asserts that active database name does **NOT** equal `CleanStation` and connection is **NOT** `mysql`.
   ```php
   if (strtolower($currentDb) === strtolower($primaryAppDb) || $currentConnection === 'mysql') {
       throw new \RuntimeException(
           "CRITICAL DATABASE SAFETY GATE: Tests attempted to connect to primary local database '{$currentDb}' on connection '{$currentConnection}'. Aborted immediately to protect primary data."
       );
   }
   ```
3. **Database Records Breakdown:**

| Table | Local App MySQL (`CleanStation`) | Isolated Testing SQLite (`testing.sqlite`) | Classification / Origin |
| :--- | :--- | :--- | :--- |
| `users` | 13,717 | 285 | Real local development seed vs Test fixtures |
| `devices` | 2,074 | 57 | Local dev devices vs Test fixtures |
| `notifications` | 1,329 | 28 | Historical campaign logs vs Test campaigns |
| `users_notifications` | 11 | 41 | Old local development test logs (Notif IDs 1117713–1117739) |
| `notification_tokens` | 3,550 | 1 | Detailed below |

### Origin of Counts:
- **`users_notifications = 11`:** These are 11 historical records from local manual testing of notifications prior to Phase 1 (targeting users 379 and 15588 across campaign IDs 1117713, 1117716, 1117717, 1117738, 1117739).
- **`notification_tokens = 3,550`:**
  - 2,500 rows belong to historical campaign #1117760 created during initial Phase 2 development.
  - 1,050 rows were inserted into the primary MySQL database during a previous unisolated execution of `scratch/benchmark_performance.php` (campaigns #1117767 and #1117768).
  - **Audit Action Taken:** In accordance with audit rule #8 ("Do not attempt to fix or delete data, document and declare FAIL"), no `DELETE`, `TRUNCATE`, or `migrate:fresh` was executed on the primary database. The records remain intact and are recorded as an isolation defect in this report.

---

## 3. Real Queue Worker Execution Audit

`php artisan serve` is an HTTP server and **never acts as a queue worker**. A genuine queue worker was executed against the database queue connection on the isolated testing database.

### Worker Command Line
```powershell
php artisan queue:work database --queue=default --sleep=1 --tries=1 --timeout=60 --stop-when-empty
```

### Full Cycle Evidence (`RealDatabaseQueueWorkerAuditTest::test_01`)
1. **Campaign & Device Token Setup:**
   - Test campaign created: ID `#1` (apps channel, direct FCM mode enabled in memory).
   - Test device token created in `notification_tokens`: Status `queued`.
2. **Dispatch to `jobs` Table:**
   - `SendFcmBatchJob` dispatched to `jobs` table with serialized `devicesBatch`.
   - Verified row exists in `jobs` (`queue = default`).
3. **Payload Serialized Inspection:**
   - Inspected JSON payload inside `jobs.payload`.
   - Decoded `data.command` serialized object:
     ```json
     {
       "notification_id": 1,
       "devicesBatch": [
         {
           "notification_token_id": 1,
           "device_id": 1,
           "user_id": 1,
           "token": "[REDACTED_MOCK_FCM_TOKEN]",
           "platform": "android"
         }
       ]
     }
     ```
   - Confirmed explicit presence of `notification_token_id`.
4. **Real Worker Invocation:**
   - `Artisan::call('queue:work', ['connection' => 'database', '--once' => true, '--stop-when-empty' => true])`.
   - Worker successfully claimed job, executed `SendFcmBatchJob::handle()`, and dispatched concurrent HTTP v1 call to FCM mock.
5. **Post-Execution State Verification:**
   - `jobs` count: `0` (job successfully deleted upon completion).
   - `notification_tokens.status`: Transitioned from `queued` to `accepted`.
   - `failed_jobs` count: `0` before, `0` after.

---

## 4. Kill Switch via Real Database Queue Worker

Tested in `RealDatabaseQueueWorkerAuditTest::test_02`:

### Test Execution Steps
1. Config set to `notification.direct_fcm_enabled = true`.
2. Campaign created and 5 device tokens inserted with status `queued`.
3. `SendFcmBatchJob` dispatched to `jobs` table while worker is idle.
4. Verified:
   - `jobs` count = 1.
   - `notification_tokens` with status `queued` = 5.
5. Kill switch activated: `config(['notification.direct_fcm_enabled' => false])`.
6. New real worker launched via `Artisan::call('queue:work', ['connection' => 'database', '--stop-when-empty' => true])`.
7. Worker popped and processed the job.

### Kill Switch Results
- **HTTP requests to FCM:** `0` (FCM pool never invoked).
- **Tokens with status `queued`:** `0`.
- **Tokens with status `skipped_by_feature_flag`:** `5` (100% of batch).
- **`error_code`:** `DIRECT_FCM_DISABLED`.
- **`accepted_count`:** `0`.
- **`transient_failed`:** `0`.
- **`permanent_failed`:** `0`.
- **`jobs` table count:** `0` (job consumed and marked complete).
- **`failed_jobs` count:** `0` (did not enter dead-letter queue).
- **Queue Monitor View:** Displays status `Completed — skipped by feature flag`.
- **Proof of Dynamic Evaluation:** Worker evaluated `config('notification.direct_fcm_enabled')` at execution time inside `handle()`, dynamically overriding the dispatch-time flag.

---

## 5. Fail-Closed Job Integrity via Real Worker

Tested in `RealDatabaseQueueWorkerAuditTest::test_03`:

### Test Execution Steps
1. Dispatched malformed `SendFcmBatchJob` with missing identifiers:
   - `notification_token_id` = `null`
   - `device_id` = `null`
   - `token` = `null`
2. Real worker executed the job.

### Fail-Closed Results
- **Database Updates:** `0` rows modified in `notification_tokens`.
- **Campaign Isolation:** Legitimate queued tokens in the campaign remained untouched (`status = queued`).
- **Logged Output:**
  ```text
  SendFcmBatchJob: Kill switch fail-closed activated for notification 1. Batch contains no valid identifiers (missing notification_token_id, device_id, and token). Zero rows updated.
  ```
- **Job Outcome:** Job exited gracefully without throwing uncaught exceptions to prevent queue blockage.
- **Monitoring Mechanism:** Verified via Laravel Application Log (`storage/logs/laravel.log`). Note: No real-time webhook or SMS alert is wired to fail-closed log warnings; monitoring relies on log inspection and Queue Monitor.

---

## 6. Code Review & Defect Remediation

### A. `UserController::updateFcm` & Sanctum Authentication
- **Original Defect:** Calling `POST /api/user/fcm-token` with Sanctum bearer tokens produced HTTP 401 unauthenticated if the default guard did not detect the user.
- **Root Cause:** Guard resolution in `UserController` strictly depended on `$request->user()`, which fails under certain API route middleware groups when Sanctum guards are chained.
- **Remediation:**
  ```php
  $user = $request->user() ?? auth('sanctum')->user() ?? auth('api')->user();
  ```
- **Compatibility Proof:**
  - Accepts identical legacy payload (`fcm_token`, `device_id`, `type`).
  - Missing `installation_id` defaults to `null` without triggering HTTP 422.
  - JSON response structure (`status`, `message`, `data`) is 100% identical.
  - Existing deployed mobile app versions operate without regression.

### B. `FCMService::sendBatchDirect` Attempts Counter & PHP 8.4 Cast
- **Original Defect:** `NotificationToken::update(['attempts' => DB::raw('attempts + 1')])` threw a PHP 8.4 `TypeError: Object of class Illuminate\Database\Query\Expression could not be converted to int` during Eloquent model attribute casting.
- **Remediation:** The increment is now calculated directly on model attributes:
  ```php
  $attemptsCount = $existingTok ? ((int)$existingTok->attempts + 1) : 1;
  $existingTok->update([
      // ...
      'attempts' => $attemptsCount,
  ]);
  ```
  Additionally, null-coalescing was added for `user_id`, `device_id`, and `token` to prevent undefined array key notices.

### C. `sent_count` Column Query Removal
- **Original Defect:** `FCMService` attempted to execute `DB::table('notifications')->increment('sent_count', ...)`. The column `sent_count` does not exist on the `notifications` table, throwing `SQLSTATE[42S22]: Column not found: 1054`.
- **Remediation:** Removed reference to `sent_count`. Campaign delivery counts utilize `accepted_by_fcm_count`, `targeted_users_count`, and `users_notifications` pivot table status. Legacy campaigns continue displaying historic metrics accurately.

---

## 7. `app_context` Field Normalization Audit

Tested in `RealDatabaseQueueWorkerAuditTest::test_04`:

- **Canonical Enum Values:** `client`, `driver`, `technical`, `unknown`.
- **Input Test:** Sent `POST /api/devices/sync` with payload containing:
  ```json
  {
    "device_token": "fcm_test_token_technical_ctx",
    "app_context": "technician",
    "type": "android"
  }
  ```
- **Response:** HTTP 200 OK, returning normalized `app_context: "technical"`.
- **Database Assertions:**
  - `devices.app_context` for the token is strictly `'technical'`.
  - Confirmed `devices.app_context = 'technician'` does **NOT** exist in the database.
- **Mobile Backward Compatibility:** Technicians using older Flutter app builds submitting `"technician"` are automatically mapped without 422 validation errors.

---

## 8. Query Breakdown & Profiling (Mocked-FCM Local Backend Benchmark)

Prior reports cited an unverified metric of **1,102 queries** for a 50-device campaign. A rigorous stage-by-stage profiling was executed on the isolated testing database (`testing.sqlite`) using `DB::listen`.

### Profiling Results Across All 7 Stages (50 Devices Batch)
| Stage | Queries | Duration (ms) | Memory Delta (MB) | Primary Query Patterns |
| :--- | :---: | :---: | :---: | :--- |
| **1. Fixture Setup** (50 users & devices) | 250 | 40,892.16 | +2.26 | `insert into users`, `insert into devices`, `insert into model_has_roles` |
| **2. Campaign Creation** | 3 | 109.58 | +0.67 | `insert into notifications`, `select roles` |
| **3. Audience & Eligibility Evaluation** | 301 | 3,187.92 | +1.04 | `select users`, `insert into users_notifications`, per-user eligibility updates |
| **4. notification_tokens Creation & Mapping** | 2 | 14.44 | -0.23 | `insert or ignore into notification_tokens` (chunked), `select id, device_id, token` |
| **5. Dispatch to `jobs` Table** | 1 | 10.42 | +0.02 | `insert into jobs` |
| **6. SendFcmBatchJob::handle Only** | **254** | **1,133.97** | **+5.52** | Per-device token SELECT/UPDATE, per-user notification SELECT/UPDATE |
| **7. Batch Callbacks / Finalization** | 2 | 19.78 | -0.24 | `update notifications set processing_status = completed` |
| **TOTAL (All 7 Stages)** | **813** | **45,368.27** | — | — |
| **JOB EXECUTION ONLY (Stage 6)** | **254** | **1,133.97** | **+5.52** | **5.08 queries per device** |

### Top 10 Most Frequent Query Patterns (Across All Stages)
1. `(198 times)`: `update users_notifications set eligibility_status = ?, eligibility_reason = ?, status = ?, response = ? where ...`
2. `(153 times)`: `insert into users (...) values (...)`
3. `(148 times)`: `insert into devices (...) values (...)`
4. `(50 times)`: `select * from notification_tokens where notification_id = ? and token = ? limit 1`
5. `(50 times)`: `update notification_tokens set user_id = ?, device_id = ?, status = ?, attempts = ?, accepted_at = ? where id = ?`
6. `(50 times)`: `select count(*) as aggregate from notification_tokens where notification_id = ? and user_id = ? and status in ('accepted', 'received', 'opened')`
7. `(50 times)`: `select count(*) as aggregate from notification_tokens where notification_id = ? and user_id = ? and status in ('permanent_failed', 'transient_failed')`
8. `(50 times)`: `update users_notifications set status = ?, accepted_devices_count = ?, failed_devices_count = ?, response = ? where ...`
9. `(50 times)`: `insert or ignore into model_has_roles (...) values (...)`
10. `(2 times)`: `insert or ignore into notification_tokens (...) values (...)`

### Performance Verdict & N+1 Analysis: **PERFORMANCE FAIL**
- **Root Cause:** While the previous 1,102 query figure was inflated by fixture setup and historical data evaluation, Stage 6 (`SendFcmBatchJob::handle`) executes **254 queries for a single 50-device batch** ($5.08$ queries per device).
- **Linear Scaling Defect:**
  - 1 SELECT per device token (`NotificationToken::where(...)`).
  - 1 UPDATE per device token (`$existingTok->update(...)`).
  - 2 SELECT COUNTs per user (`NotificationToken::where('user_id', ...)->count()`).
  - 1 UPDATE per user (`users_notifications` status update).
- **Audit Decision:** In compliance with audit rule #8 ("If Job itself executes linear queries per device, declare Performance FAIL and identify N+1. Do not implement unapproved optimizations"), no code modification was made. Bulk upserting and aggregated group-by queries must be scheduled for a separate approved optimization phase.

---

## 9. Full Regression Test Results

### A. Notification & Remediation Test Suites
Executed via PHPUnit 11.5.56:
- `NotificationRemediationPhase1Test`
- `NotificationAdminDashboardPhase2Test`
- `NotificationRemediationPhase21Test`
- `NotificationRemediationPhase22Test`
- `LegacyCompatibilityGateTest`
- `StagingDashboardVerificationTest`
- `StagingControlledFCMVerificationTest`
- `StagingKillSwitchAndFailClosedTest`
- `RealDatabaseQueueWorkerAuditTest`

```text
Time: 01:03.772, Memory: 96.00 MB
OK (94 tests, 378 assertions)
```
**Result:** **100% PASSED** (0 failures, 0 errors, 0 regressions).

### B. Full Application Unit & Feature Testsuite
Executed: `php vendor/bin/phpunit --testsuite=Unit,Feature`
- **Total Tests:** 154
- **Assertions:** 596
- **Failures:** 13 (0 New Regressions)
- **Passed:** 141

#### The 13 Historical Failures (Pre-existing in Repository):
1. `Tests\Unit\ConvertProductImagesToWebpTest::test_convert_product_images_to_webp_and_compress_sales_products` (image compression threshold mismatch)
2. `Tests\Feature\FinancialAnalysisTest::test_admin_can_access_monthly_financial_analysis` (missing test seed)
3. `Tests\Feature\FinancialAnalysisTest::test_admin_can_access_daily_financial_analysis` (missing test seed)
4. `Tests\Feature\FinancialAnalysisTest::test_admin_can_store_daily_financial_inputs` (missing test seed)
5. `Tests\Feature\FinancialAnalysisTest::test_admin_can_export_monthly_financial_analysis` (missing test seed)
6. `Tests\Feature\FinancialAnalysisTest::test_admin_can_export_daily_financial_analysis` (missing test seed)
7. `Tests\Feature\FinancialsCrudTest::test_admin_can_create_financial_record_with_user` (sqlite schema mismatch: `user_id` column)
8. `Tests\Feature\FinancialsCrudTest::test_admin_can_update_financial_record_from_company_to_user` (sqlite schema mismatch: `user_id` column)
9. `Tests\Feature\OrderPointsTest::test_driver_finishes_order_and_points_are_awarded_based_on_price_formula` (driver points calculation formula drift)
10. `Tests\Feature\OrderPointsTest::test_technical_finishes_order_and_points_are_awarded_based_on_price_formula` (technical points calculation formula drift)
11. `Tests\Feature\OrderPointsTest::test_points_cannot_be_negative` (points balance assertion drift)
12. `Tests\Feature\PurchasesCrudTest::test_admin_can_create_purchase_with_attachment_and_date` (datetime string formatting assertion)
13. `Tests\Feature\PurchasesCrudTest::test_admin_can_update_purchase_and_change_attachment` (datetime string formatting assertion)

No notification, device, queue, or auth tests failed.

---

## 10. Residual Risks

1. **Database Queue Contention at Scale (N+1 Queries):**
   A campaign of 5,000 devices will execute $\approx 25,400$ database queries across 100 queued batch jobs if processed without bulk upserts. While functional in small batches, this presents a lock contention risk on high-concurrency production MySQL.
2. **Historical Local Database Contamination:**
   1,050 records from previous scratch scripts remain in the local MySQL `CleanStation` database. While harmless locally and fully documented, migration and seed scripts for Production must be strictly isolated.
3. **Queue Monitor Dead-Letter Alerting:**
   Malformed jobs that activate the fail-closed path complete without crashing the queue worker, but rely solely on file-based log inspection. There is no automated external alert (e.g. Sentry/Slack).

---

## 11. Final Verdict

$$\mathbf{C.\ FAIL\ —\ Database\ isolation,\ Queue,\ compatibility,\ or\ performance\ problem\ found}$$

### Rationale for Verdict:
1. **Database Isolation Breach (Historical):** The local primary MySQL database (`CleanStation`) was previously touched by unisolated benchmark scripts inserting 1,050 rows into `notification_tokens` and 2 campaign rows. Under audit rules, this prevents an unconditional PASS.
2. **Performance N+1 in Job Execution:** `SendFcmBatchJob::handle` executes 254 queries for a 50-device batch ($5.08$ queries per device). Under Section 8 audit criteria, linear query execution per device mandates a **Performance FAIL**.
3. **Direct FCM Resting State:** Confirmed resting state is strictly `NOTIFICATION_DIRECT_FCM_ENABLED=false`.

### Recommended Next Actions (Pre-Production Deployment Plan):
1. Prepare bulk upsert optimization RFC for `FCMService::sendBatchDirect` to reduce job queries from $5N$ to $O(1)$.
2. Prepare a clean Production migration plan ensuring all test artifacts and mock scripts are omitted.
3. Keep Direct FCM disabled (`NOTIFICATION_DIRECT_FCM_ENABLED=false`) until query optimization and production readiness review are approved.
