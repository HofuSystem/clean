# Clean Station — Notification System Audit Report
**Date:** 2026-09-26
**Scope:** Read-only technical audit
**Auditor:** Antigravity AI
**Status:** Complete — awaiting human review before any implementation

---

## 1. Executive Summary

Clean Station has **13,711** user records. When sending an FCM bulk push notification, the dashboard column labelled **"delivered"** shows approximately **2,065–2,100**.

This number does **not** mean 2,100 users received or opened the notification.

**What it actually measures:** The number of FCM topic-subscription requests that the Google IID API accepted without error — i.e., tokens that were **successfully registered to a topic** before the topic message was sent. It is reflected in `users_notifications.status = 'sent'`.

The root cause of the 14,000 vs 2,100 gap is simple and confirmed:
**11,646 users (85%) have no device token in the `devices` table and are excluded from FCM sending entirely.**

---

## 2. Repositories and Components Inspected

| Component | Location | Available |
|---|---|---|
| Laravel backend | d:\programming\projects\Hofu\CleanStation | YES |
| Admin dashboard (Blade views) | packages/core/notification/src/resources/views/ | YES |
| Flutter customer app | Not present in this workspace | NOT AVAILABLE |
| Firebase config | base_path('fcm.json') at runtime | Referenced |
| Queue workers | QUEUE_CONNECTION=database (.env) | YES |

> Flutter application is not available in this workspace. Conclusions about token registration and permission handling are based on what the Laravel backend records only.

---

## 3. Database Schema

### Table: devices

Source: packages/core/users/src/database/migrations/2025_01_04_002850_create_devices_table.php

| Column | Type | Nullable | Notes |
|---|---|---|---|
| id | bigint PK | No | Auto-increment |
| device_token | varchar(255) | No | FCM token |
| type | enum: ios, android, huawei | No | Platform |
| user_id | bigint FK -> users | Yes | cascadeOnDelete |
| created_at / updated_at | timestamp | No | |
| deleted_at | timestamp | Yes | Soft deletes enabled |

No unique constraint on device_token. One user can have at most one row per platform type (updateOrCreate uses user_id + type as key). No active/inactive flag. No last_used_at column.

### Table: notifications

Source: packages/core/notification/src/database/migrations/2025_01_04_024837_create_notifications_table.php + subsequent migrations

| Column | Type | Nullable | Notes |
|---|---|---|---|
| id | bigint PK | No | |
| types | json | No | Send channels e.g. ["apps"], ["whats_app"] |
| for | enum: all, users, email, phone | No | Recipient mode |
| for_data | varchar(1000) | Yes | JSON array of user IDs or CSV of emails/phones |
| title | varchar(255) | No | |
| body | varchar(1000) | No | |
| payload | longText | Yes | Extra JSON for app |
| register_from/to | date | Yes | Filter by registration date |
| orders_from/to | date | Yes | Filter by order date |
| orders_min/max | integer | Yes | Filter by order count |
| sender_id | bigint FK -> users | Yes | |
| deleted_at | timestamp | Yes | Soft deletes |

### Table: users_notifications (pivot / inbox / delivery log)

Source: packages/core/notification/src/database/migrations/2025_01_04_024846_create_users_notifications_table.php + 2025_06_28_111254 migration

| Column | Type | Nullable | Notes |
|---|---|---|---|
| id | bigint PK | No | |
| notifications_type | varchar morph | No | Always Core\Notification\Models\Notification |
| notifications_id | bigint morph | No | FK to notifications.id |
| user_id | bigint FK -> users | Yes | cascadeOnDelete |
| read_at | datetime | Yes | In-app read timestamp |
| status | enum: pending, sent, failed | No | Default: pending |
| response | longText | Yes | Raw FCM/API response |

This table serves dual purposes: in-app notification inbox (read_at) and FCM delivery attempt log (status, response).

### Table: notification_tokens (per-send token log)

Source: packages/core/notification/src/database/migrations/2025_10_04_121515_create_notification_tokens.php

| Column | Notes |
|---|---|
| id | bigint PK |
| topic | FCM topic name |
| token | Individual device token |
| status | 'pending', 'success', or 'failed' |
| title | Notification title |
| notification_id | Links to notifications.id |
| created_at/updated_at | timestamps |

---

## 4. End-to-End Notification Flow

### Step 1: Admin creates notification
File: packages/core/notification/src/Controllers/Dashboard/NotificationsController.php
Method: storeOrUpdate() -> NotificationsService::storeOrUpdate() -> Notification::updateOrCreate()

### Step 2: Observer fires on created
File: packages/core/notification/src/Observers/NotificationObserver.php
Method: created() -> SendNotificationJob::dispatch($notification)

### Step 3: Queue job executes
File: packages/core/notification/src/Jobs/SendNotificationJob.php
Queue: database | Tries: 3
-> NotificationsManger::getInstance()->sendNotification($notification)

### Step 4: Recipient selection
File: packages/core/notification/src/Helpers/NotificationsManger.php
Method: getNotificationUserQuery()

For for='all' (no filters): SELECT * FROM users  (all 13,711 — no is_active filter, no verified_at filter)
For for='users': SELECT * FROM users WHERE id IN (...)

### Step 5: Token collection (FCM only)
Method: getNotificationUsersDevices($users)

Reconstructed SQL:
SELECT users.id, fullname, phone, email, device_token
FROM users
INNER JOIN devices ON devices.user_id = users.id
WHERE user_id IN (...)
  AND devices.device_token IS NOT NULL
  AND devices.id IN (SELECT MAX(id) FROM devices GROUP BY user_id)

FINDING: Query does NOT filter deleted_at IS NULL on devices. Soft-deleted rows may be included.
RESULT: Only ONE token per user (most recently inserted row by MAX id).

### Step 6: Sync pivot table
$notification->users()->sync($ids) creates users_notifications rows with status='pending' for all targeted users.

### Step 7: Mark no-token users as failed
DB::table('users_notifications')
    ->where('notifications_id', $notification->id)
    ->where('status', 'pending')
    ->whereIn('user_id', $userIdsWithoutTokens)
    ->update(['status' => 'failed', 'response' => 'No device token']);

### Step 8: Dispatch based on count
File: packages/core/notification/src/Helpers/NotificationsManger.php, sendApps()

- <= 5 tokens: FCMService::sendToDevice() per token (synchronous)
- 6-100 tokens: FCMService::sendToCustomTopic() topic='under-100-function-v5' (synchronous)
- > 100 tokens: dispatch(new SendApps()) -> queued -> NotificationsSender::fcm() -> FCMService::sendToCustomTopic() topic='fcm-function-v5'

### Step 9: FCM topic send
File: packages/core/notification/src/Services/FCMService.php
Method: sendToCustomTopic()

1. Unsubscribe old tokens: POST https://iid.googleapis.com/iid/v1:batchRemove
2. Subscribe new tokens in chunks of 1000: POST https://iid.googleapis.com/iid/v1:batchAdd
3. Check IID result per token — non-empty result = notWorkingToken
4. Send ONE topic message: POST https://fcm.googleapis.com/v1/projects/{id}/messages:send
5. Save results to notification_tokens table
6. Update users_notifications: working tokens -> status='sent', not working -> status='failed'
7. Device::whereIn('device_token', $notWorkingTokens)->forceDelete() — permanently delete invalid tokens

### Step 10: Dashboard display
File: packages/core/notification/src/Services/NotificationsService.php, dataTable()

->withCount(['users as sent_count' => function ($query) {
    $query->where('users_notifications.status', 'sent');
}])

File: packages/core/notification/src/resources/views/pages/notifications/list.blade.php
Column header: @lang("delivered") but maps to data-name="sent_count"

---

## 5. Exact Meaning of the ~2,100 Number

The number displayed in the "delivered" column = COUNT of users_notifications rows with status='sent'.

status='sent' is set when the Google IID batchAdd API accepted the token into the FCM topic subscription. It does NOT confirm:
- FCM delivered the message to the device
- The device received the notification
- The notification was displayed
- The user saw or opened it

---

## 6. Database Aggregate Counts (Local DB, 2026-09-26)

### Devices
| Metric | Count |
|---|---|
| Total device rows (incl. soft-deleted) | 2,074 |
| Soft-deleted rows | 5 |
| Active device rows | 2,069 |
| Null or empty token | 11 |
| Unique token values | 2,058 |
| iOS devices | 1,940 |
| Android devices | 129 |
| Users with multiple device rows | 4 |
| Duplicate token values | 1 |
| Updated in last 7 days | 0 |
| Updated in last 30 days | 1 |
| Updated in last 90 days | 458 |
| Updated in last 180 days | 835 |
| Updated in last 365 days | 1,640 |
| NEVER updated since creation | 2,008 |

### Users
| Metric | Count |
|---|---|
| Total users | 13,711 |
| Phone-verified | 13,493 |
| With active device token | 2,065 |
| WITHOUT any device token | 11,646 |

### users_notifications (local test data only)
| Metric | Count |
|---|---|
| Total rows | 11 |
| status=sent | 10 |
| status=failed | 1 |
| status=pending | 0 |

### Failed Jobs
| Metric | Count |
|---|---|
| Failed queue jobs | 0 |

---

## 7. Metric Truth Table

| Metric / Label | Source | Calculation | Proves | Does NOT Prove |
|---|---|---|---|---|
| Total customers | users table | COUNT(*) FROM users | Registered accounts | App installation, activity, reachability |
| Targeted users | getNotificationUserQuery() | Users matching for/for_data filter | Admin intended audience | Who received anything |
| Users with tokens | devices JOIN | COUNT DISTINCT user_id WHERE deleted_at IS NULL | Has registered FCM token | Token valid, app installed, notifications on |
| "Delivered" (dashboard) | users_notifications.status='sent' | COUNT where status='sent' | IID subscription accepted | FCM delivery, receipt, display, open |
| Failed | users_notifications.status='failed' | COUNT where status='failed' | Token rejected OR no token | User blocked notifications |
| Delivered | NOT MEASURED | — | — | — |
| Received | NOT MEASURED | — | — | — |
| Opened | NOT MEASURED | — | — | — |
| Read (in-app) | users_notifications.read_at | IS NOT NULL | User opened in-app inbox | Push notification was seen |

---

## 8. Confirmed Causes of the Gap

Directly proven by code and database:

1. **11,646 users (85%) have no device token.** They are immediately marked status='failed' with response='No device token'. This is the dominant cause.
   Evidence: getNotificationUsersDevices() in NotificationsManger.php; DB aggregate count.

2. **Only one token per user is selected** (MAX(id) per user_id in getNotificationUsersDevices). Multi-platform users get only one chance.
   Evidence: NotificationsManger.php getNotificationUsersDevices() subquery.

3. **Dashboard label "delivered" misrepresents what is measured.** It shows IID subscription count, not FCM delivery.
   Evidence: list.blade.php data-name="sent_count" with @lang("delivered"); NotificationsService.dataTable().

---

## 9. Likely Causes

Supported by evidence but not fully proven:

1. **2,008 of 2,069 tokens have never been updated since creation** — 97% of tokens may be stale. These pass IID subscription but FCM may silently reject delivery.

2. **Reinstalled apps do not auto-refresh tokens** unless user explicitly logs out and back in. No backend mechanism detects reinstalls.

3. **getNotificationUsersDevices() may include soft-deleted device rows** (missing WHERE deleted_at IS NULL).

---

## 10. Possible Causes

Technically possible, lacking full evidence:

1. Notification permission denied — user has token but OS silently drops message. Indistinguishable from successful delivery.
2. Wrong Firebase project — some tokens may be from a different project (e.g. test env). SENDER_ID_MISMATCH not explicitly handled.
3. iOS APNs misconfiguration — 94% of tokens are iOS; APNs errors are not logged per-token.
4. App uninstalled without logout — token stays in DB until IID rejects it on next send.

---

## 11. Ruled-Out Causes

1. **Queue job failures** — failed_jobs table has 0 rows.
2. **System only targeting 2,100 users** — system does target all users but marks the rest failed immediately due to no token.
3. **for_data overflow causing send to fail** — this error occurs at notification creation, not at send time.

---

## 12. Missing Information

1. Flutter source code not available — cannot confirm token registration timing, onTokenRefresh handling, permission request logic.
2. Production notification_tokens table not inspected (0 rows locally).
3. Firebase Console delivery data not accessible.
4. Whether User model has a global scope filtering inactive users — not confirmed.

---

## 13. Risks

| Risk | Severity |
|---|---|
| Dashboard shows "delivered" but means "IID subscribed" | HIGH |
| 97% of device tokens never refreshed (may be stale) | HIGH |
| No notification permission status tracked | HIGH |
| getNotificationUsersDevices() may include soft-deleted rows | MEDIUM |
| for_data varchar(1000) overflow on large user selections | HIGH |
| iOS APNs delivery unverifiable from Laravel | HIGH |
| No last_app_open tracking | MEDIUM |
| Token from wrong Firebase project undetectable | MEDIUM |

---

## 14. Evidence Index

| Evidence | File |
|---|---|
| devices table schema | packages/core/users/src/database/migrations/2025_01_04_002850_create_devices_table.php |
| notifications table schema | packages/core/notification/src/database/migrations/2025_01_04_024837_create_notifications_table.php |
| users_notifications schema | packages/core/notification/src/database/migrations/2025_01_04_024846_create_users_notifications_table.php |
| status+response columns | packages/core/notification/src/database/migrations/2025_06_28_111254_add_notificaion_status_to_users_notifications.php |
| notification_tokens schema | packages/core/notification/src/database/migrations/2025_10_04_121515_create_notification_tokens.php |
| Observer dispatches job on created | packages/core/notification/src/Observers/NotificationObserver.php — created() |
| Recipient query logic | packages/core/notification/src/Helpers/NotificationsManger.php — getNotificationUserQuery() |
| ONE token per user (MAX id) | packages/core/notification/src/Helpers/NotificationsManger.php — getNotificationUsersDevices() |
| Sync pivot + mark no-token failed | packages/core/notification/src/Helpers/NotificationsManger.php — sendApps() |
| Size-based dispatch routing | packages/core/notification/src/Helpers/NotificationsManger.php — sendApps() |
| FCM topic subscription + send | packages/core/notification/src/Services/FCMService.php — sendToCustomTopic() |
| forceDelete invalid tokens | packages/core/notification/src/Services/FCMService.php — sendToCustomTopic() |
| sent_count query | packages/core/notification/src/Services/NotificationsService.php — dataTable() |
| Dashboard column labelled "delivered" | packages/core/notification/src/resources/views/pages/notifications/list.blade.php |
| NotificationsResource sent_count | packages/core/notification/src/DataResources/NotificationsResource.php — toArray() |
| Token stored on login/verify | packages/core/users/src/Controllers/Api/AuthenticationController.php — verify(), login() |
| Token deleted on logout | packages/core/users/src/Controllers/Api/AuthenticationController.php — logout() |
| QUEUE_CONNECTION=database | .env |
