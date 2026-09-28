# تقرير إغلاق المرحلة 2.2 — Remediation Phase 2.2 Closure Report

**التاريخ:** 2026-09-27  
**المشروع:** CleanStation Backend Notification System  
**الحالة العامة المعتمدة:**
- **Phase 2.2 Remediation:** `PASS`
- **Phase 2 Acceptance:** `PASS`
- **Direct FCM:** `Disabled` (`NOTIFICATION_DIRECT_FCM_ENABLED=false`)
- **Staging:** `Not started`
- **Mobile (Flutter):** `Not started`

---

## 1. ملخص تنفيذي (Executive Summary)

تم إنجاز وتوثيق مرحلة **Remediation Phase 2.2** بنطاق محدود وصارم لمعالجة القصورين اللذين ظهرا في تدقيق المرحلة 2.1:
1. **تدقيق مصدر ومفهوم المعرفات في `devicesBatch`**: إثبات المسار الحقيقي للبيانات من الـ Pipeline، وإلغاء الخلط بين `devices.id` و`notification_tokens.id`، وتمرير `notification_token_id` الصريح في كل Job Payload.
2. **جعل الـ Kill Switch دقيقاً ومغلقاً عند الفشل (Exact and Fail-Closed)**:
   - التحديث ينحصر حصرياً في سجلات الدفعة الحالية عبر `notification_token_id`.
   - استخدام مطابقة أزواج مشروطة `(device_id, token)` معاً للمسارات القديمة لمنع الـ Cross-pairing.
   - تطبيق مبدأ **Fail-Closed**: إذا غابت المعرفات، يُحدث 0 صف، ويُسجل Log خطأ واضح، ويُمنع نهائياً تحويل بقية queued الحملة إلى skipped.
3. **تصحيح تصنيف Legacy مقابل Direct FCM**:
   - إعطاء الأولوية المطلقة للـ Immutable Snapshot (`payload.delivery_channel`).
   - منع تغيير الـ Snapshot بعد الإنشاء.
   - حظر تصنيف الحملات التاريخية كـ Direct FCM لمجرد وجود سجلات في `notification_tokens` (مثل الحملة المرجعية 1120558).

---

## 2. تدقيق Payload الحقيقي للـ Job ومصدر كل حقل

### 2.1 مسار تدفق البيانات (Data Pipeline Trace)
1. **استخراج الأجهزة المؤهلة (`RecipientEligibilityService::evaluateEligibility`)**:
   يقوم الاستعلام بجلب أجهزة المستخدمين المستهدفين من جدول `devices`.
   يتم تجميع الأجهزة وتكوين كل عنصر كالتالي:
   ```php
   $eligibleDevices->push([
       'device_id'       => $dev->id,               // المعرف الأساسي لجدول devices
       'user_id'         => $user->id,              // المعرف الأساسي لجدول users
       'token'           => $dev->device_token,     // التوكن الفعلي للجهاز
       'platform'        => $dev->type,             // المنصة: android أو ios
       'installation_id' => $dev->installation_id,  // معرف التثبيت الفريد
       'app_context'     => $dev->app_context,      // سياق التطبيق: client | driver | technical
   ]);
   ```
   **ملاحظة قاطعة:** الناتج الخام للـ Pipeline **لا يحتوي** على مفتاح `id`، ولا يحتوي على `notification_token_id`.

2. **التسجيل المسبق وحقن `notification_token_id` (`NotificationsManger::dispatchAppPushes`)**:
   يتم إدراج صفوف في جدول `notification_tokens` بالحالة `queued`:
   ```php
   DB::table('notification_tokens')->insertOrIgnore($tokenInserts);
   ```
   ثم استرجاع المعرفات المولدة وربطها بالدفعة:
   ```php
   $insertedTokens = DB::table('notification_tokens')
       ->where('notification_id', $notification->id)
       ->select(['id', 'device_id', 'token'])
       ->get();

   $tokenMap = [];
   foreach ($insertedTokens as $tok) {
       $tokenMap[($tok->device_id ?? '') . '#' . $tok->token] = $tok->id;
   }

   foreach ($eligibleDevices as &$dev) {
       $key = ($dev['device_id'] ?? '') . '#' . ($dev['token'] ?? '');
       if (isset($tokenMap[$key])) {
           $dev['notification_token_id'] = $tokenMap[$key];
       }
   }
   unset($dev);
   ```

3. **نموذج Payload الحقيقي في `SendFcmBatchJob`**:
   عند عمل `array_chunk($eligibleDevices, 50)` وإرسالها للوظيفة، يكون شكل العنصر الفعلي في `$devicesBatch`:
   ```json
   {
       "notification_token_id": 48291,
       "device_id": 1402,
       "user_id": 883,
       "token": "eX_910FaT...Kz_real_fcm_token",
       "platform": "android",
       "installation_id": "inst_7b9c1d04-4e2a",
       "app_context": "client"
   }
   ```

### 2.2 جدول المعاني والدليل على مفهوم كل حقل

| الحقل | المصدر الحقيقي | الجدول وقيد المفتاح | المعنى والدليل |
|---|---|---|---|
| `notification_token_id` | `notification_tokens.id` | المفتاح الأساسي لجدول `notification_tokens` | **المفتاح الحصري** لاستهداف السجل وتحديث حالته. |
| `device_id` | `devices.id` | المفتاح الأساسي لجدول `devices` | يشير إلى صف الجهاز المسجل، **ولا يمثل** سجل الإشعار. |
| `id` | موروث من `devices.id` إذا وُجد في payloads قديمة | جدول `devices` | **يمنع قطعياً استخدامه** في `whereIn('notification_tokens.id', ...)` منعاً للخلط بين مفاتيح جدولين مختلفين. |
| `token` | `devices.device_token` | عمود `device_token` | رمز دفع FCM المسجل للجهاز. |
| `installation_id` | `devices.installation_id` | عمود `installation_id` | معرف فريد لتثبيت التطبيق على الجهاز الفيزيائي. |

---

## 3. تفاصيل الكود قبل وبعد (Code Refactoring Diffs)

### 3.1 معالجة الـ Kill Switch داخل `SendFcmBatchJob.php`

#### قبل (Phase 2.1 — كان يحتوي على خطر خلط المعرفات، والـ Cross-pair، والـ Fail-Open):
```php
// كود Phase 2.1 السابق:
$tokenRecordIds = array_filter(array_merge(
    array_column($this->devicesBatch, 'id'),
    array_column($this->devicesBatch, 'notification_token_id')
));
$deviceIds = array_filter(array_column($this->devicesBatch, 'device_id'));
$tokens = array_filter(array_column($this->devicesBatch, 'token'));

$query = NotificationToken::where('notification_id', $this->notification->id)
    ->where('status', 'queued');

if (!empty($tokenRecordIds)) {
    $query->whereIn('id', $tokenRecordIds);
} elseif (!empty($deviceIds)) {
    $query->whereIn('device_id', $deviceIds);
    if (!empty($tokens)) {
        $query->whereIn('token', $tokens); // خطر cross-pair!
    }
} elseif (!empty($tokens)) {
    $query->whereIn('token', $tokens);
}

// إذا كانت المعرفات فارغة: ينفذ تحديثاً عاماً لكل queued في الحملة (خطر Fail-Open)!
$query->update([...]);
```

#### بعد (Phase 2.2 — حصر نطاق دقيق، منع Cross-pair، وسلوك Fail-Closed صارم):
```php
// كود Phase 2.2 المعتمد:
// 1. الأسلوب المفضل والصريح: المطابقة بمعرف سجل التوكن الصريح
$tokenRecordIds = collect($this->devicesBatch)
    ->pluck('notification_token_id')
    ->filter()
    ->unique()
    ->values()
    ->all();

if (!empty($tokenRecordIds)) {
    NotificationToken::where('notification_id', $this->notification->id)
        ->where('status', 'queued')
        ->whereIn('id', $tokenRecordIds)
        ->update([
            'status' => 'skipped_by_feature_flag',
            'error_code' => 'DIRECT_FCM_DISABLED',
            'updated_at' => now(),
        ]);

    Log::warning("SendFcmBatchJob: Kill switch activated for notification {$this->notification->id}. Exact batch updated by notification_token_id (" . count($tokenRecordIds) . " tokens).");
    return;
}

// 2. المطابقة التاريخية للوظائف القديمة العالقة في الـ Queue: مطابقة أزواج صارمة لمنع cross-pairing
$validPairs = [];
$tokensOnly = [];
foreach ($this->devicesBatch as $dev) {
    $devId = $dev['device_id'] ?? null;
    $tok = $dev['token'] ?? null;
    if (!empty($devId) && !empty($tok)) {
        $validPairs[] = ['device_id' => $devId, 'token' => $tok];
    } elseif (empty($devId) && !empty($tok)) {
        $tokensOnly[] = $tok;
    }
}

if (!empty($validPairs) || !empty($tokensOnly)) {
    NotificationToken::where('notification_id', $this->notification->id)
        ->where('status', 'queued')
        ->where(function ($query) use ($validPairs, $tokensOnly) {
            foreach ($validPairs as $pair) {
                $query->orWhere(function ($sub) use ($pair) {
                    $sub->where('device_id', $pair['device_id'])
                        ->where('token', $pair['token']);
                });
            }
            if (!empty($tokensOnly)) {
                $query->orWhere(function ($sub) use ($tokensOnly) {
                    $sub->whereNull('device_id')
                        ->whereIn('token', array_unique($tokensOnly));
                });
            }
        })
        ->update([
            'status' => 'skipped_by_feature_flag',
            'error_code' => 'DIRECT_FCM_DISABLED',
            'updated_at' => now(),
        ]);

    Log::warning("SendFcmBatchJob: Kill switch activated for notification {$this->notification->id}. Legacy batch updated using exact (device_id, token) pairs (" . (count($validPairs) + count($tokensOnly)) . " pairs).");
    return;
}

// 3. Fail-closed: في حال غياب المعرفات بالكامل، يمنع منعاً باتاً التحديث العام
Log::error("SendFcmBatchJob: Kill switch fail-closed activated for notification {$this->notification->id}. Batch contains no valid identifiers (missing notification_token_id, device_id, and token). Zero rows updated.");
return;
```

---

### 3.2 تصنيف قناة الإرسال داخل `Notification.php`

#### قبل (Phase 2.1 — كان يصنف أي حملة فيها توكنات كـ Direct FCM):
```php
public function getDeliveryChannel(): string
{
    // خطأ فادح: الحملات القديمة كانت تملك notification_tokens وظهرت كلها كـ direct_fcm!
    if ($this->relationLoaded('notificationTokens') ? $this->notificationTokens->count() > 0 : $this->notificationTokens()->exists()) {
        return 'direct_fcm';
    }

    if ($this->accepted_by_fcm_count > 0) {
        return 'direct_fcm';
    }

    $payload = is_array($this->payload) ? $this->payload : json_decode($this->payload ?? '{}', true);
    if (!empty($payload['delivery_channel'])) {
        return $payload['delivery_channel'];
    }

    return 'legacy_topic';
}
```

#### بعد (Phase 2.2 — أولوية مطلقة للـ Snapshot وحماية كاملة للحملات التاريخية):
```php
public function getDeliveryChannel(): string
{
    $payload = is_array($this->payload) ? $this->payload : json_decode($this->payload ?? '{}', true);

    // 1. Immutable snapshot explicitly saved at campaign creation takes highest priority
    if (!empty($payload['delivery_channel'])) {
        if ($payload['delivery_channel'] === 'direct_fcm') {
            return 'direct_fcm';
        }
        if (in_array($payload['delivery_channel'], ['legacy_topic', 'legacy'], true)) {
            return 'legacy_topic';
        }
        return (string) $payload['delivery_channel'];
    }

    // 2. Historical campaigns without snapshot:
    // لا تصنف direct بناء على وجود صفوف notification_tokens العادية (مثل الحملة 1120558).
    // Direct FCM يثبت فقط بدليل صريح لا يقبل الشك: accepted_by_fcm_count > 0 أو topic='direct'.
    if ($this->accepted_by_fcm_count > 0) {
        return 'direct_fcm';
    }

    if ($this->relationLoaded('notificationTokens')
        ? $this->notificationTokens->where('topic', 'direct')->count() > 0
        : $this->notificationTokens()->where('topic', 'direct')->exists()) {
        return 'direct_fcm';
    }

    return 'legacy_topic';
}
```

#### حماية ثبات الـ Snapshot (`NotificationsManger.php`):
تم تعديل `sendLegacyApps()` و`dispatchAppPushes()` ليتحققا أولاً عبر `if (empty($payload['delivery_channel']))` قبل حفظ القناة، مما يضمن عدم تعديل الـ Snapshot بعد إنشاء الحملة نهائياً.

---

## 4. نتائج اختبارات التحقق من القواعد الدقيقة

### 4.1 اختبار Fail-Closed مع المعرفات الفارغة (Empty Identifiers)
- **السيناريو:** حملة تحتوي على 5 سجلات توكن في حالة `queued`. تم تشغيل وظيفة بـ Batch يحتوي على مصفوفات فارغة أو حقول مجهولة بدون معرفات.
- **النتيجة:**
  - تم تنفيذ فرع الـ Fail-Closed.
  - تم تحديث **0 صفوف**.
  - بقيت جميع السجلات الـ 5 بحالة `queued`.
  - سُجل Log الخطأ التحذيري بوضوح.
  - **النتيجة: PASS** (`test_04`, `test_05`).

### 4.2 اختبار الحماية من التقاطع غير المقصود (Cross-Pair Protection)
- **السيناريو:**
  - في قاعدة البيانات:
    - سجل 1: `(device 1, token A)`
    - سجل 2: `(device 2, token B)`
    - سجل 3: `(device 1, token B)` — تقاطع متداخل
    - سجل 4: `(device 2, token A)` — تقاطع متداخل
  - تم إرسال Batch يحتوي على:
    `[device_id: 1, token: 'A'], [device_id: 2, token: 'B']` (دون `notification_token_id`).
- **النتيجة:**
  - تم تحديث السجلين 1 و2 فقط إلى `skipped_by_feature_flag`.
  - بقي السجلان 3 و4 في حالة `queued`.
  - أثبت الاستعلام المشروط `orWhere(where(device)->where(token))` استبعاد أي مطابقة متقاطعة كلياً.
  - **النتيجة: PASS** (`test_08`).

### 4.3 اختبار الحملة التاريخية الشبيهة بـ 1120558
- **السيناريو:**
  - محاكاة الحملة المرجعية: لا تملك snapshot في `payload`، عدد الإرسال القديم 2,142، وتملك سجلات في `notification_tokens` بحالة `success` أو `sent` بموضوع عام `topic = 'all'`.
- **النتيجة:**
  - `$notification->getDeliveryChannel()` أرجعت `legacy_topic`.
  - `$notification->getDeliveryMetricLabel()` أرجعت `Legacy Topic Subscription`.
  - شاشة تفاصيل الحملة وشاشة الـ Queue Monitor عرضتا القناة كـ `Legacy Topic` دون أي ادعاء مضلل لـ FCM.
  - **النتيجة: PASS** (`test_14`, `test_15`).

---

## 5. نتائج جميع Test Suites

تم تشغيل جميع أجنحة الاختبارات المؤتمتة وتأكيد اجتيازها بالكامل دون أي تراجع:

```
Test Suites Executed:
1. Tests\Feature\NotificationRemediationPhase1Test:       19 Passed  (75 assertions)
2. Tests\Feature\NotificationAdminDashboardPhase2Test:     14 Passed  (73 assertions)
3. Tests\Feature\NotificationRemediationPhase21Test:      15 Passed  (49 assertions)
4. Tests\Feature\NotificationRemediationPhase22Test:      16 Passed  (58 assertions)
--------------------------------------------------------------------------------------
Total Notification Remediation & Dashboard Tests:         64 Passed (255 assertions)
Total Regressions:                                         0
```

---

## 6. قائمة الـ 13 فشلاً التاريخياً المعتمدة (Exact JUnit XML Baseline)

لتصحيح التضارب الوارد في تقرير المرحلة 2.1 (حيث ورد خطأً اسم `FinancialRecordTest`)، هذه هي القائمة الحرفية المطابقة لمخرجات الـ JUnit XML الناتجة من تشغيل:
`php artisan test --testsuite=Unit,Feature --log-junit scratch/junit_final.xml`

```
1. Tests.Unit.ConvertProductImagesToWebpTest > test_convert_product_images_to_webp_and_compress_sales_products
2. Tests.Feature.FinancialAnalysisTest > test_admin_can_access_monthly_financial_analysis
3. Tests.Feature.FinancialAnalysisTest > test_admin_can_access_daily_financial_analysis
4. Tests.Feature.FinancialAnalysisTest > test_admin_can_store_daily_financial_inputs
5. Tests.Feature.FinancialAnalysisTest > test_admin_can_export_monthly_financial_analysis
6. Tests.Feature.FinancialAnalysisTest > test_admin_can_export_daily_financial_analysis
7. Tests.Feature.FinancialsCrudTest > test_admin_can_create_financial_record_with_user
8. Tests.Feature.FinancialsCrudTest > test_admin_can_update_financial_record_from_company_to_user
9. Tests.Feature.OrderPointsTest > test_driver_finishes_order_and_points_are_awarded_based_on_price_formula
10. Tests.Feature.OrderPointsTest > test_technical_finishes_order_and_points_are_awarded_based_on_price_formula
11. Tests.Feature.OrderPointsTest > test_points_cannot_be_negative
12. Tests.Feature.PurchasesCrudTest > test_admin_can_create_purchase_with_attachment_and_date
13. Tests.Feature.PurchasesCrudTest > test_admin_can_update_purchase_and_change_attachment
```

**الملخص:**
- إجمالي اختبارات `Unit,Feature`: **124 اختباراً**.
- الناجحة: **111 اختباراً** (ارتفعت من 95 بفضل 16 اختباراً جديداً في المرحلة 2.2).
- الفاشلة: **13 اختباراً** حصرياً (وهي نفس خط الأساس التاريخي المسبق المتعلق بالنقاط، وتحويل الصور، والماليات والمشتريات القديمة، دون أي علاقة أو تأثير من نظام الإشعارات).

---

## 7. القيود الصارمة الملتزم بها (Strict Boundaries Confirmation)

- [x] **Direct FCM Disabled:** القيمة الحالية في بيئة التشغيل والإعدادات هي `NOTIFICATION_DIRECT_FCM_ENABLED=false`.
- [x] **No FCM Live Call:** لم يتم إجراء أي اتصال حقيقي بسيرفرات Google FCM.
- [x] **No Migrations:** لم يتم إنشاء أي ملفات ترحيل جديدة في هذه المرحلة.
- [x] **No Destructive DB Changes:** لم يتم حذف أو تخريب أي بيانات موجودة في قاعدة البيانات.
- [x] **Flutter Untouched:** لم يتم تعديل أي ملف في مشروع الموبايل (Flutter).
- [x] **Export Authorization Intact:** جميع حمايات الـ 403 للـ Show Staff والعملاء واختبارات الـ Streaming لا تزال تعمل وتجتاز 100%.
- [x] **Staging Not Started:** لم تبدأ بيئة Staging بعد.
- [x] **Mobile Not Started:** لم تبدأ مرحلة تكامل تطبيقات الموبايل بعد.

---

## 8. الخاتمة والإعلان النهائي

بناءً على الاختبارات القطعية والبراهين البرمجية المرفقة، تم إغلاق جميع أوجه القصور بالكامل:

```
Phase 2.2 Remediation: PASS
Phase 2 Acceptance:    PASS
Direct FCM:            Disabled
Staging:               Not started
Mobile:                Not started
```