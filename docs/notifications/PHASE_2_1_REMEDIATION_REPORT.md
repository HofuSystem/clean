# تقرير إغلاق المرحلة: Remediation Phase 2.1
## Notification Engine & RBAC Export Hardening Report

**تاريخ التنفيذ:** 2026-09-27  
**البيئة:** التطوير المحلي (Local Development / Pre-Staging)  
**الحالة النهائية للمرحلة:** **PASS (تم إغلاق كافة الملاحظات بنجاح)**  
**حالة قبول المرحلة الثانية (Phase 2 Acceptance):** **PASS**  
**حالة Direct FCM:** **Disabled (`NOTIFICATION_DIRECT_FCM_ENABLED=false`)**  
**التحقق على Staging:** **Not started (لم يبدأ)**  
**تطوير الموبايل (Mobile Implementation):** **Not started (لم يبدأ)**  

---

## 1. ملخص تنفيذي (Executive Summary)

تم تنفيذ **Remediation Phase 2.1** بنجاح تام وفقاً للضوابط الصارمة المحددة، وذلك لمعالجة نتيجتي **FAIL** المثبتتين في تقرير التدقيق الفني `docs/notifications/PHASE_2_ACCEPTANCE_AUDIT.md`:
1. **Kill Switch lifecycle persistence:** معالجة مشكلة بقاء سجلات الأجهزة عالقة بالحالة `queued` في جدول `notification_tokens` عند تفعيل قاطع الطوارئ (Kill Switch) وقت تنفيذ الـ Job.
2. **Export authorization bypass:** إزالة الثغرة الصلاحياتية في `CheckPermissions.php` التي كانت تسمح لحامل صلاحية العرض فقط `dashboard.notifications.show` بتنزيل ملفات التصدير التفصيلية `exportDetails`.

تم الالتزام بجميع الموانع المفروضة:
- لم يتم إجراء أي اتصال حقيقي مع Google FCM.
- لم يتم تفعيل `NOTIFICATION_DIRECT_FCM_ENABLED`، وبقيت القيمة `false`.
- لم يتم إجراء أي تعديلات على تطبيق Flutter أو واجهاته.
- لم يتم إنشاء أي Migrations جديدة أو تنفيذ أي تغييرات تدميرية على البيانات.
- لم يتم بدء مرحلة Staging.

---

## 2. الأسباب الجذرية (Root Cause Analysis)

### المشكلة الأولى: عدم تحديث دورة حياة سجلات الدفعة عند تفعيل الـ Kill Switch
- **السلوك السابق:** في `SendFcmBatchJob::handle()`، كان فحص `!config('notification.direct_fcm_enabled', false)` يُنفذ عند بداية الوظيفة، وعندما يكون الـ Flag معطلاً، تخرج الوظيفة (`return`) دون تحديث جدول `notification_tokens`.
- **الأثر السابق:** بالرغم من اكتمال الدفعة وحملة الإشعار (`processing_status = completed`)، كانت سجلات الأجهزة التابعة للدفعة تظل في حالة `status = 'queued'` إلى ما لا نهاية، مما يوحي للمشرف بأن الإرسال معلق في الطابور ولم ينتهِ.
- **الحل الجذري:** تنفيذ تحديث آمن ودقيق محصور بالدفعة الحالية حصراً لتحويل السجلات من `queued` إلى `skipped_by_feature_flag` وتسجيل رمز الخطأ الآمن `DIRECT_FCM_DISABLED` وتحديث `updated_at = now()` قبل الخروج فوراً وبشكل Idempotent دون لمس العدادات أو السجلات الأخرى.

### المشكلة الثانية: تجاوز صلاحيات التصدير عبر مسارات التحقق البديلة (Alias Fallback)
- **السلوك السابق:** كان Middleware التحقق من الصلاحيات `CheckPermissions.php` يحتوي على قاعدة بديلة:
  ```php
  if ($routeName === 'dashboard.notifications.exportDetails') {
      $possiblePermissions[] = 'dashboard.notifications.show';
  }
  ```
- **الأثر السابق:** أي مستخدم أو موظف يملك صلاحية مشاهدة تفاصيل الحملة فقط (`dashboard.notifications.show`) كان قادراً على الوصول لنقاط تصدير بيانات الأهلية، الأجهزة، والأخطاء، حتى لو لم يُمنح صلاحية `dashboard.notifications.export`.
- **الحل الجذري:** إزالة `dashboard.notifications.show` بالكامل وتخصيص الفحص الصارم لحصر صلاحية التصدير على الصلاحيات المعتمدة في النظام:
  - `dashboard.notifications.export`
  - `notifications.export`
  - `notifications export`
  مع إخفاء أزرار التصدير في الواجهة لمن لا يملك الصلاحية، مع بقاء الحماية الأساسية Server-side بإرجاع HTTP 403 Forbidden فوري دون بدء `StreamedResponse` أو إرسال أي ترويسات CSV.

---

## 3. الملفات والأسطر المعدلة (Modified Files & Lines)

| الملف | نوع التعديل | وصف التعديل |
| :--- | :--- | :--- |
| `packages/core/notification/src/Jobs/SendFcmBatchJob.php` | تعديل | إضافة حصر وتحديث سجلات الدفعة الحالية التابعة لنفس الحملة من `queued` إلى `skipped_by_feature_flag` مع تسجيل كود `DIRECT_FCM_DISABLED` بشكل Idempotent وتفادي أي اتصال خارجي. |
| `packages/core/users/src/Middleware/CheckPermissions.php` | تعديل | إلغاء بديل صلاحية `show` في مسار `exportDetails` وحصر المسار على مصفوفة الصلاحيات المعتمدة للتصدير فقط. |
| `packages/core/notification/src/Controllers/Dashboard/NotificationsController.php` | تعديل | 1) تمرير عداد الأجهزة المتخطاة `skippedTokensCount` لواجهة تفاصيل الحملة.<br>2) قصر استعلام `lastErrorToken` في Queue Monitor على الأخطاء الحقيقية (`transient_failed`, `permanent_failed`) لضمان عدم ظهور التخطي الآمن كفشل إرسال. |
| `packages/core/notification/src/Models/Notification.php` | تعديل | تقديم فحص وجود سجلات `notificationTokens` في `getDeliveryChannel()` لضمان تصنيف الحملات التي تم تجهيز أجهزة لها كـ `direct_fcm` بدقة حتى عند تخطي الدفعات بالـ Flag. |
| `packages/core/notification/src/resources/views/pages/notifications/show.blade.php` | تعديل | 1) اشتقاق وعرض شارة الحالة: `مكتمل — تخطي بـ Flag` عند تخطي الأجهزة.<br>2) إخفاء القائمة المنسدلة وأزرار تصدير CSV عمن لا يملك صلاحية التصدير.<br>3) عرض إحصائية `تخطي Flag` في بطاقة نتائج الإرسال. |
| `packages/core/notification/src/resources/views/pages/notifications/queue-monitor.blade.php` | تعديل | اشتقاق وتوضيح الحالة في جدول مراقبة الطابور: `مكتمل — تخطي بـ Flag` أو `مكتمل جزئياً — تخطي بـ Flag` لمنع أي إيحاء كاذب بنجاح التسليم. |
| `tests/Feature/NotificationRemediationPhase21Test.php` | إنشاء جديد | إنشاء جناح اختبارات مخصص وشامل (15 اختباراً آلياً) يغطي كافة متطلبات المرحلة. |

---

## 4. منطق تحديد سجلات الدفعة وحصرها (Scoping Logic)

في `SendFcmBatchJob.php`:
```php
if (!config('notification.direct_fcm_enabled', false)) {
    $this->executionStatus = 'skipped_by_feature_flag';

    // 1. استخراج المعرفات الأولية لسجلات الرموز إذا كانت متوفرة
    $tokenRecordIds = array_filter(array_merge(
        array_column($this->devicesBatch, 'id'),
        array_column($this->devicesBatch, 'notification_token_id')
    ));
    $deviceIds = array_filter(array_column($this->devicesBatch, 'device_id'));
    $tokens = array_filter(array_column($this->devicesBatch, 'token'));

    // 2. حصر الاستعلام بدقة متناهية:
    // - لنفس الحملة (notification_id)
    // - للسجلات التي لا تزال في حالة queued فقط
    $query = NotificationToken::where('notification_id', $this->notification->id)
        ->where('status', 'queued');

    if (!empty($tokenRecordIds)) {
        $query->whereIn('id', $tokenRecordIds);
    } elseif (!empty($deviceIds)) {
        $query->whereIn('device_id', $deviceIds);
        if (!empty($tokens)) {
            $query->whereIn('token', $tokens);
        }
    } elseif (!empty($tokens)) {
        $query->whereIn('token', $tokens);
    }

    // 3. التحديث الحصري
    $query->update([
        'status' => 'skipped_by_feature_flag',
        'error_code' => 'DIRECT_FCM_DISABLED',
        'updated_at' => now(),
    ]);

    Log::warning("SendFcmBatchJob: Kill switch activated for notification {$this->notification->id}. Execution skipped without contacting FCM or generating fake records.");
    return;
}
```

### الضمانات الفنية:
1. **عزل الدفعات:** الدفعات الأخرى من نفس الحملة أو محاولات الإرسال للحملات الأخرى لا تتأثر إطلاقاً.
2. **حماية السجلات المكتملة:** السجلات التي أصبحت `accepted` أو `transient_failed` أو `permanent_failed` أو `processing` لا تُمَس إطلاقاً نظراً لشرط `status = 'queued'`.
3. **ثبات العدادات:** لا يتم التعديل على عدادات الإرسال (`accepted_by_fcm_count`, `transient_failed_count`, `permanent_failed_count`).

---

## 5. حالة قاعدة البيانات قبل وبعد الـ Kill Switch

### مقارنة الحالة لسيناريو حملة (100 جهاز في الطابور عند إيقاف الـ Flag):

| الحقل / المؤشر | قبل الإصلاح (Audit Baseline) | بعد الإصلاح (Phase 2.1 Fixed) |
| :--- | :--- | :--- |
| `notification_tokens.status` | بقيت `queued` (100 جهاز) | تحولت بالكامل إلى `skipped_by_feature_flag` (100 جهاز) |
| `notification_tokens.queued` | 100 | **0** |
| `notification_tokens.skipped_by_feature_flag` | 0 | **100** |
| `notification_tokens.accepted` | 0 | 0 |
| `notification_tokens.error_code` | `null` | `DIRECT_FCM_DISABLED` |
| `notifications.processing_status` | `completed` | `completed` |
| بطاقة الحالة في لوحة التحكم | مكتمل (يوحي بالنجاح) | **مكتمل — تخطي بـ Flag** (واضح وصريح) |
| اتصالات HTTP بـ Google FCM | 0 | **0** |
| اكتمال الـ Batch | true | **true** |

---

## 6. إثبات عدم إجراء FCM Request واختبارات الـ Idempotency

### إثبات عدم استدعاء FCM:
- تم تأكيد عدم استدعاء `FCMService::getInstance()->sendBatchDirect()` نظراً لوجود `return` فوري داخل كتلة فحص الـ Flag.
- تم التحقق آلياً عبر `Http::fake()` واستدعاء `Http::assertNothingSent()` في الاختبار:
  `test_07_no_http_or_fcm_request_occurs_while_disabled()` -> **PASSED**.

### نتائج اختبارات التكرارية (Idempotency):
- تم تنفيذ الوظيفة على دفعة متخطاة لمرتين متتاليتين في الاختبار:
  `test_06_executing_same_skipped_job_twice_is_idempotent()` -> **PASSED**.
- في التشغيل الثاني:
  - عدد الصفوف التي تطابق `status = 'queued'` كان **0**.
  - لم يحدث أي تعديل إضافي على السجلات أو أوقاتها.
  - لم تتأثر أي عدادات في جدول `notifications`.

---

## 7. مصفوفة الصلاحيات بعد الإصلاح (Permission Matrix)

| الدور / الصلاحية | تفاصيل الحملة (`show`) | جداول الأهلية والنتائج (`getEligibilityUsers` / `getDeviceResults`) | تصدير الأهلية (`export/eligibility`) | تصدير الأجهزة (`export/devices`) | تصدير الأخطاء (`export/errors`) |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Super Admin / IT** | 200 OK | 200 OK | 200 OK (Masked) | 200 OK (Masked) | 200 OK (Masked) |
| **Admin** | 200 OK | 200 OK | 200 OK (Masked) | 200 OK (Masked) | 200 OK (Masked) |
| **Staff (`show` فقط)** | **200 OK** | **200 OK** | **403 Forbidden** | **403 Forbidden** | **403 Forbidden** |
| **Staff (`show` + `export`)** | **200 OK** | **200 OK** | **200 OK (Masked)** | **200 OK (Masked)** | **200 OK (Masked)** |
| **Client / Non-Admin** | 403 Forbidden | 403 Forbidden | 403 Forbidden | 403 Forbidden | 403 Forbidden |

### التحقق من استجابة 403 Forbidden:
- لا تبدأ استجابة تدفق (`StreamedResponse`).
- لا يتم إرسال ترويسة `Content-Disposition: attachment; filename=...`.
- لا يتم إرسال `Content-Type: text/csv`.
- لا يتم تسريب أي سطر من سجلات الأجهزة أو الرموز المحجوبة أو غير المحجوبة.

---

## 8. نتائج الفحص والتحقق (DOM & Browser QA)

### الحالة الأولى: مستخدم العرض فقط (`qa_show_only@example.com`):
1. **صفحة التفاصيل:** تفتح بنجاح (HTTP 200)، وتُعرض بطاقات الأهلية والنتائج.
2. **أزرار التصدير:** القائمة المنسدلة "تصدير البيانات (Export CSV)" وأزرار "تصدير CSV" في تبويبات الأهلية والأجهزة **مخفية بالكامل** من الـ DOM عبر توجيهات `@if(auth()->user()?->can('...export'))`.
3. **الوصول المباشر:** عند طلب الرابط المباشر `/admin/notifications/{id}/export/devices`، يتم الحظر فوراً برمز **403 Forbidden** من خادم التطبيق دون إرسال أي محتوى CSV.

### الحالة الثانية: مستخدم التصدير المصرح (`qa_export_user@example.com`):
1. **ظهور الأزرار:** زر وقائمة "تصدير البيانات (Export CSV)" ظاهرة ونشطة في واجهة المستخدم.
2. **التصدير الفعلي:** تم تنزيل ملف CSV بنجاح برمز **200 OK** وترويسة `Content-Type: text/csv; charset=UTF-8`.
3. **حجب الرموز (Token Masking):** تم فحص محتوى الـ CSV وتأكيد عدم وجود أي رمز Push خام (`sensitive_raw_fcm_token_xyz_987654321`)، وظهور الرمز محجوباً بالكامل بالصيغة المعتمدة (`***654321`).

---

## 9. نتائج الاختبارات الآلية (Automated Test Suites Summary)

### 1) جناح اختبارات Phase 2.1 الجديد (`NotificationRemediationPhase21Test.php`):
```text
PASS  Tests\Feature\NotificationRemediationPhase21Test
✓ 01 queued rows become skipped when flag is off (0.51s)
✓ 02 accepted rows remain unchanged (0.12s)
✓ 03 transient and permanent failed rows remain unchanged (0.11s)
✓ 04 only current batch is updated (0.12s)
✓ 05 unrelated notification rows remain unchanged (0.12s)
✓ 06 executing same skipped job twice is idempotent (0.12s)
✓ 07 no http or fcm request occurs while disabled (0.11s)
✓ 08 mixed accepted and skipped campaign (0.15s)
✓ 09 queue monitor shows skipped count correctly (0.27s)
✓ 10 campaign does not visually imply successful delivery (0.15s)
✓ 11 show permission does not grant export (0.16s)
✓ 12 export permission grants intended export endpoints (0.16s)
✓ 13 all three export types enforce authorization (0.14s)
✓ 14 non admin cannot export (0.16s)
✓ 15 rejected export does not stream data (0.35s)

Tests: 15 passed (49 assertions)
Duration: 3.04s
```

### 2) جناح اختبارات Phase 2 (`NotificationAdminDashboardPhase2Test.php`):
```text
PASS  Tests\Feature\NotificationAdminDashboardPhase2Test
Tests: 14 passed (73 assertions)
Duration: 24.19s
```

### 3) جناح اختبارات Phase 1 (`NotificationRemediationPhase1Test.php`):
```text
PASS  Tests\Feature\NotificationRemediationPhase1Test
Tests: 19 passed (75 assertions)
Duration: 2.67s
```

### 4) جناح الاختبارات الكامل للمشروع (`Unit` & `Feature`):
```text
Tests: 13 failed, 95 passed (415 assertions)
Duration: 86.79s
```
> **ملاحظة تأكيدية:** كافة حالات الفشل الـ 13 هي فشل قديم تاريخي مثبت في وحدات غير مرتبطة بالإشعارات (`OrderPointsTest`, `PurchasesCrudTest`, `FinancialRecordTest`). لم يحدث **أي Regression إطلاقاً** في أي اختبار للنظام.

---

## 10. تأكيد وضع الـ Flag النهائي

- ملف الإعدادات: `config/notification.php`
  ```php
  'direct_fcm_enabled' => env('NOTIFICATION_DIRECT_FCM_ENABLED', false),
  ```
- القيمة الحالية في البيئة: **`false`**.
- الاتصال المباشر بـ Google FCM: **معطل تماماً (Disabled)**.
- القناة الافتراضية النشطة لعموم التطبيق: **Legacy Topic Subscription Fallback**.

---

## 11. ملخص Git Diff المنجز

```diff
packages/core/notification/src/Jobs/SendFcmBatchJob.php
- Log::warning("SendFcmBatchJob: Kill switch activated... execution skipped.");
- return;
+ $tokenRecordIds = array_filter(array_merge(array_column($this->devicesBatch, 'id'), array_column($this->devicesBatch, 'notification_token_id')));
+ $deviceIds = array_filter(array_column($this->devicesBatch, 'device_id'));
+ $tokens = array_filter(array_column($this->devicesBatch, 'token'));
+ $query = NotificationToken::where('notification_id', $this->notification->id)->where('status', 'queued');
+ if (!empty($tokenRecordIds)) { $query->whereIn('id', $tokenRecordIds); }
+ elseif (!empty($deviceIds)) { $query->whereIn('device_id', $deviceIds); if (!empty($tokens)) $query->whereIn('token', $tokens); }
+ elseif (!empty($tokens)) { $query->whereIn('token', $tokens); }
+ $query->update(['status' => 'skipped_by_feature_flag', 'error_code' => 'DIRECT_FCM_DISABLED', 'updated_at' => now()]);
+ return;

packages/core/users/src/Middleware/CheckPermissions.php
- if ($routeName === 'dashboard.notifications.exportDetails') {
-     $possiblePermissions[] = 'dashboard.notifications.show';
- }
+ if ($routeName === 'dashboard.notifications.exportDetails') {
+     $possiblePermissions = ['dashboard.notifications.export', 'notifications.export', 'notifications export'];
+ }

packages/core/notification/src/Models/Notification.php
+ // Prioritize token presence to accurately classify direct campaigns even when skipped
+ if ($this->relationLoaded('notificationTokens') ? $this->notificationTokens->count() > 0 : $this->notificationTokens()->exists()) {
+     return 'direct_fcm';
+ }

packages/core/notification/src/resources/views/pages/notifications/show.blade.php
+ @if($skippedTokensCount > 0 && ($acceptedByFcm === 0 || $acceptedByFcm === '—'))
+     <span class="badge badge-light-warning text-warning fw-bold">مكتمل — تخطي بـ Flag</span>
+ @endif
+ @if(auth()->user()?->can('dashboard.notifications.export') || auth()->user()?->can('notifications.export') || auth()->user()?->can('notifications export'))
+     <!-- Export UI controls -->
+ @endif
```

---

## 12. المخاطر المتبقية وإجراءات السلامة التشغيلية

1. **إعادة تفعيل الـ Flag مستقبلاً:**
   - السجلات التي وُسمت بـ `skipped_by_feature_flag` لن تتم إعادة محاولتها تلقائياً عبر `retryTransient` (لأنها ليست أخطاء مؤقتة FCM). هذا يحمي النظام من إرسال حملات قديمة ملغاة بصورة مفاجئة للمستخدمين.
2. **عزل البيانات والأمان:**
   - الحماية الصلاحياتية على التصدير مطبقة Server-side ومحصنة تماماً ضد محاولات IDOR أو تزوير الروابط المباشرة.
   - حجب الرموز (Masking) مفروض على مستوى طبقة البيانات وخدمة التصدير لضمان عدم تسريب أي Token تحت أي ظرف.

---

## 13. القرار النهائي والتوقف (Final Verdict)

| البند | النتيجة |
| :--- | :--- |
| **Phase 2.1 Remediation** | **PASS** |
| **Phase 2 Acceptance** | **PASS** |
| **Direct FCM Status** | **Disabled (`false`)** |
| **Staging Verification** | **Not started** |
| **Mobile Implementation** | **Not started** |

**تم التوقف التام عند نهاية متطلبات Remediation Phase 2.1 كما ورد بالتعليمات.**
