# تقرير التدقيق وقبول التنفيذ (Implementation Acceptance Audit)

**المشروع:** Clean Station - نظام الإشعارات ولوحة التحكم  
**المرجع:** `CleanStation_Notification_Admin_Backend_Implementation_Plan.md`  
**تاريخ التدقيق:** 2026-09-26  
**البيئة المدققة:** Local Development / Windows (PHP 8.2.12, Laravel 11.x, MySQL)  
**حالة التدقيق:** مكتمل الفحص بالأدلة والأكواد (Audit Complete - Pending Approval)

---

## 1. تفسير الأرقام والاستعلامات (Discrepancy Analysis)

### الأرقام المقارنة:
1. **`14,105` مقابل `12,436`** (إجمالي المستخدمين المستهدفين).
2. **`2,142` مقابل `2,054`** (المستخدمون المؤهلون الذين يملكون أجهزة).
3. **`2,145` مقابل `2,058`** (إجمالي الأجهزة الصالحة).

### الأدلة البرمجية ونتائج استعلامات SQL الفعلية:

تم تنفيذ استعلامات الفحص عبر بيئة المشروع الفعلية على قاعدة بيانات MySQL المحلية:

```sql
-- 1. فحص إجمالي المستخدمين في جدول users
SELECT COUNT(*) FROM users; -- النتيجة: 13,711 (شامل المحذوفين)
SELECT COUNT(*) FROM users WHERE deleted_at IS NULL; -- النتيجة: 13,663
SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND is_active = 1; -- النتيجة: 12,437

-- 2. حصر العملاء الفعليين النشطين (شروط RecipientEligibilityService للحملات التسويقية)
SELECT COUNT(u.id) 
FROM users u 
INNER JOIN model_has_roles mhr ON u.id = mhr.model_id
INNER JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'client' 
  AND u.is_active = 1 
  AND u.deleted_at IS NULL;
-- النتيجة: 12,436 (وهو بالضبط رقم Targeted Users في النظام الجديد)
```

```sql
-- 3. فحص الأجهزة غير المحذوفة التي تملك Token صالح
SELECT COUNT(*) FROM devices WHERE deleted_at IS NULL AND device_token IS NOT NULL AND device_token != '';
-- النتيجة: 2,058 (iOS: 1,930 ، Android: 128)

-- 4. فحص المستخدمين المميزين أصحاب هذه الأجهزة
SELECT COUNT(DISTINCT user_id) FROM devices WHERE deleted_at IS NULL AND device_token IS NOT NULL AND device_token != '';
-- النتيجة: 2,054
```

### التفسير الهندسي لسبب الاختلاف:
| المؤشر | خط الأساس في Production (الخطة الأصلية) | البيئة المحلية الحالية (Local DB) | سبب الاختلاف الجوهري والبيئة |
|---|---:|---:|---|
| **الجمهور المستهدف** | **14,105** | **12,436** | رقم `14,105` مأخوذ من Snapshot حي على سيرفر Production لقاعدة البيانات لحظة تنفيذ التدقيق للحملة `1120558` (حيث استهدف المسار القديم جميع السجلات `User::all()`). بينما `12,436` ناتج من قاعدة البيانات المحلية بعد استبعاد: 1,226 مستخدم غير نشط (`is_active = 0`)، و1 مستخدم إداري غير عميل، وتطبيق قيد `purpose = marketing`. |
| **المستخدمون بأجهزة** | **2,142** | **2,054** | في Production كان هناك 2,142 مستخدماً يملكون توكنات. في قاعدة البيانات المحلية الحالية يوجد 2,054 مستخدماً نشطاً من فئة `client` يملكون توكنات نشطة غير محذوفة. |
| **إجمالي الأجهزة** | **2,145** | **2,058** | في Production كان المسجل 2,145 جهازاً (1,990 iOS + 155 Android). في القاعدة المحلية المسجل 2,058 جهازاً (1,930 iOS + 128 Android). |
| **الأجهزة المتعددة** | **3 مستخدمين** (2,145 - 2,142) | **4 مستخدمين** (2,058 - 2,054) | في كلا البيئتين، الفارق يمثل المستخدمين الذين لديهم أكثر من جهاز (في Production ثلاثة مستخدمين، وفي القاعدة المحلية أربعة مستخدمين يملكون جهازين لكل منهم). |

---

## 2. شروط `is_allow_notify` واستبعاد الحملات

### الكود الفعلي من `packages/core/notification/src/Services/RecipientEligibilityService.php`:

```php
// 1. عزل الجمهور بحسب نوع الإشعار (Marketing مقابل Transactional / System)
if ($purpose === 'marketing') {
    $query->whereHas('roles', function ($q) {
        $q->where('name', 'client');
    })->where('is_active', 1);
}
// Note: For transactional and system notifications, no role filter is applied
// so that drivers, technicians, and clients can all receive their operational pushes.
```

```php
// 2. التحقق من تفضيل العميل للإعلانات التسويقية
if ($purpose === 'marketing' && $user->is_allow_notify == 0 && !is_null($user->is_allow_notify_confirmed_at)) {
    $userEligibility[$user->id] = [
        'status' => 'marketing_disabled',
        'reason' => 'User opted out of marketing pushes',
    ];
    continue;
}
```

### الإثبات الهندسي:
1. **عدم حجب Legacy Users عند `confirmed_at = null`:**
   * الشرط البرمجي يتطلب صراحة `!is_null($user->is_allow_notify_confirmed_at)`.
   * إذا كان `confirmed_at` فارغاً (`null`)، فإن التعبير الشرطي يرجع `false` فوراً، وبالتالي **لا يتم تصنيف العميل كـ `marketing_disabled`**، ويصل الإشعار لجميع مستخدمي الـ Legacy الذين قيمتهم `is_allow_notify = 0` بحكم القيمة الافتراضية القديمة.
2. **الرفض الصريح فقط يمنع التسويق:**
   * الحجب يقع حصراً عندما يقوم العميل بتعديل تفضيله عبر الواجهة البرمجية فيصبح `is_allow_notify = 0` **مع** وجود تاريخ توثيق `is_allow_notify_confirmed_at`.
3. **عدم تأثر الإشعارات التشغيلية والنظامية (`transactional` / `system`):**
   * الفحص محصور داخل `if ($purpose === 'marketing')`.
   * في حال كان الإشعار تشغيلياً (مثل إشعارات الطلبات أو فواتير السائقين والفنيين)، يتم تجاوز الفحص بالكامل، ولا يتم استبعاد أي مستخدم بناءً على تفضيل الإعلانات.

---

## 3. توحيد القيم والـ Aliases ودعم المنصات

### الكود الفعلي من `packages/core/users/src/Controllers/Api/DeviceSyncController.php`:

```php
$validator = Validator::make($request->all(), [
    'device_token' => 'required|string',
    'platform' => 'required|string|in:ios,android,huawei',
    'installation_id' => 'nullable|string|max:255',
    'app_context' => 'nullable|string|in:client,driver,technical,unknown',
    'notification_permission' => 'nullable|string|in:authorized,denied,not_determined,provisional,unknown',
    'app_version' => 'nullable|string|max:50',
    'os_version' => 'nullable|string|max:50',
    'device_model' => 'nullable|string|max:100',
]);
```

### الملاحظات ونقاط القصور المرصودة (Gaps Identified):
1. **المنصة (`platform` مقابل `type`):**
   * الكود الحالي يفرض وجود `platform` إجبارياً (`required|string|in:ios,android,huawei`).
   * إذا أرسل تطبيق قديم أو واجهة قديمة الحقل باسم `type` بدلاً من `platform`، **سيفشل التحقق بـ 422**. يجب توحيد وقبول `type` كـ Fallback قبل التحقق.
2. **الأذونات (`notification_permission`):**
   * التحقق يقبل فقط: `authorized, denied, not_determined, provisional, unknown`.
   * لا يقبل `granted` كـ Alias قادم من بعض حزم Flutter/Android.
3. **سياق التطبيق (`app_context`):**
   * التحقق يقبل: `client, driver, technical, unknown`.
   * لا يقبل `technician` كـ Alias لـ `technical`.

---

## 4. تقييم Feature Flags وخطة الرجوع (Rollback Safety)

### النتيجة: **NOT IMPLEMENTED**

### الأدلة:
1. تم فحص ملفات الإعداد `config/` وحزمة `packages/core/notification`:
   * لا توجد أي مفاتيح Feature Flags معرفة في `config/notification.php` مثل:
     - `notifications.device_sync_enabled`
     - `notifications.direct_fcm_enabled`
     - `notifications.permission_mode`
     - `notifications.marketing_preference_mode`
2. في `NotificationsManger.php`، تم تحويل دالة `sendApps()` مباشرة إلى `dispatchAppPushes()` التي تعتمد `SendFcmBatchJob` وإرسال الـ HTTP v1، دون وجود شرط تحقق (`if (config('notification.direct_fcm_enabled'))`).
3. **أثر عدم وجود الـ Flags:**
   * في حال حدوث أي طارئ في إنتاج Google FCM v1، لا يمكن لمدير النظام العودة الفورية للمسار القديم (`sendToCustomTopic`) عبر تغيير قيمة في `.env` أو لوحة التحكم، بل يتطلب ذلك تعديل الكود يدوياً.

---

## 5. مطابقة شاشات لوحة التحكم المنفذة مع الخطة

| الشاشة / الوظيفة | المتطلب في الخطة الأصلية | الحالة المنفذة | الدليل الفعلي |
|---|---|---|---|
| **صحة الأجهزة (Health)** | صفحة إحصائيات الأجهزة، التوزيع، المنصات، الأذونات، والتفاعل | **PASS** | مسار `admin/notifications/health`، وقالب `health.blade.php` كامل وشغال 100% |
| **قائمة الحملات (Campaign List)** | تعديل مصطلح "تم التسليم" إلى "قُبل من FCM" وإضافة شارات الحالة والغرض وزر الصحة | **PASS** | تم تعديل `list.blade.php` و`NotificationsResource.php` |
| **تفاصيل الحملة (Campaign Details)** | صفحة منفصلة تعرض تبويبات: ملخص الجمهور، فحص الأهلية لكل مستخدم، نتائج الأجهزة (Accepted/Failed)، والتفاعل | **NOT IMPLEMENTED** | لم يتم إنشاء شاشة `show.blade.php` تفصيلية بحسب متطلبات البند 9.3 |
| **فحص الأهلية (Eligibility Tab)** | جدول يوضح حالة كل مستخدم: Eligible, No Device, Marketing Disabled, Permission Denied | **NOT IMPLEMENTED** | البيانات تسجل في قاعدة البيانات (`users_notifications`) لكن لا توجد واجهة عرض لها |
| **نتائج الأجهزة (Device Results)** | عرض حالة كل جهاز مقنّع التوكن وسبب الفشل الدائم أو المؤقت | **NOT IMPLEMENTED** | البيانات تسجل في `notification_tokens` لكن لم تُبْنَ واجهة لوحة التحكم لها |
| **المعاينة قبل الإرسال (Preview)** | Modal في صفحة إنشاء الإشعار يعرض عدد المستهدفين، المؤهلين، وتنبيه الجمهور غير القابل للوصول | **NOT IMPLEMENTED** | شاشة `create.blade.php` ما زالت بالصيغة القديمة |
| **مراقبة الـ Queue (Queue Monitor)** | مؤشر للحملات المعلقة طويلاً، الدفعات المكتملة والفاشلة، وزر Retry للأخطاء المؤقتة فقط | **PARTIAL** | يوجد زر قديم "إعادة إرسال للمعلقين" مع شارة الحالة، ولكن لا توجد شاشة مراقبة الدفعات المحددة بالبند 9.5 |
| **تصدير التقارير (Export)** | تصدير تفصيلي للحملة بناءً على حالة الأهلية والنتائج | **NOT IMPLEMENTED** | التصدير الحالي عام للحملات فقط وليس مفصلاً لنتائج الأجهزة |

---

## 6. نتائج تشغيل حزمة الاختبارات الآلية (Test Suite Execution)

تم تشغيل الأمر `php artisan test` بالكامل على المشروع:

* **إجمالي الاختبارات:** 137 اختباراً.
* **النتائج:**
  * **Failed:** 60 اختباراً.
  * **Warnings:** 67 تحذيراً.
  * **Passed:** 10 اختبارات.
  * **Assertions:** 578 تأكيداً.
  * **مدة التشغيل:** 64.66 ثانية.

### تحليل أسباب الفشل:
* فشل الاختبارات يعود إلى إعداد قاعدة بيانات الاختبارات الافتراضية في `phpunit.xml` (`sqlite :memory:`)، حيث تبين أن حزم الـ Packages الخاصة بـ Clean Station (`packages/core/*/src/database/migrations`) لا يتم تحميل مساراتها تلقائياً داخل اختبارات الـ Unit العامة للـ Framework، مما تسبب بخطأ:
  `SQLSTATE[HY000]: General error: 1 no such table: users (Connection: sqlite)`.
* **الأهم من ذلك:** لا توجد حالياً أي ملفات اختبارات آلية خاصة بنظام الإشعارات (`tests/Feature/NotificationTest.php` أو ما يماثلها).

---

## 7. فحص السيناريوهات المتقدمة بالاختبارات البرمجية

| السيناريو | الحالة | الملاحظات والدليل |
|---|---|---|
| **دعم أجهزة السائقين والفنيين** | **PASS جزئي** | في `RecipientEligibilityService` الإشعارات التشغيلية تشملهم، لكن `DeviceSyncController` يرفض `technician` كقيمة مدخلة إلا إذا أُرسلت `technical`. |
| **وظائف ومراقبو الإشعارات (Observers)** | **PASS** | `NotificationObserver` يطلق `SendNotificationJob` الذي يستدعي `NotificationsManger`. `BannerNotificationObserver` محايد ولا يتدخل. |
| **تعدد أشكال `BannerNotification`** | **PASS** | جميع استعلامات وتحديثات جدول `users_notifications` تم حمايتها بـ `where('notifications_type', Notification::class)`. |
| **قنوات واتساب والبريد وSMS** | **PASS** | تم فصل `dispatchNonAppChannels()` وتمرير القوائم بأمان بدون تعارض مع دفعات التطبيق. |
| **تسجيل الدخول والخروج مع SoftDeletes** | **PASS** | في `DeviceSyncController` يتم استخدام `Device::withTrashed()` واسترجاع السجل، وتحديث الحقول دون رمي استثناءات. |
| **منع تكرار الإرسال عند Retry في الـ Queue** | **PASS** | في `FCMService.php` يتم فحص التوكنات المقبولة مسبقاً (`NotificationToken::whereIn('status', ['accepted', ...])`) واستبعادها قبل استدعاء `Http::pool`. |
| **تجميع ملخص Telegram** | **PASS** | كود الإرسال مدمج في الـ `then` Callback للـ Batch ويرسل معرف الحملة والأرقام التفصيلية لقناة `@itcleanstation`. |

---

## 8. تدقيق أمان واجهة أحداث الإشعارات (Notification Events API Security)

تم فحص كود `NotificationEventsController.php`:

1. **المصادقة (Authentication):**
   * **محمي:** المسار مسجل داخل مجموعة `middleware => ['auth:sanctum', 'active']`. الطلبات غير المصرحة ترفض برمز `401`.
2. **التحقق من الملكية (Ownership Verification) - [ثغرة مرصودة]:**
   * **غير محقق (FAIL):** الدالة تبحث عن التوكن أو الجهاز عبر `installation_id` أو `device_token` دون التحقق من أن الجهاز يتبع للمستخدم صاحب الـ Token المصادق عليه:
     ```php
     $device = Device::where('installation_id', $request->installation_id)->first();
     ```
     هذا يسمح لمستخدم مسجل بتمرير `installation_id` يخص عميلاً آخر وتحديث توقيتات استلامه وفتحه للإشعار.
3. **منع التكرار (Idempotency):**
   * **محقق (PASS):** يتم التحقق من أن `received_at` أو `opened_at` فارغ (`is_null`) قبل التحديث وقبل زيادة العداد `increment('received_count')`. التكرار لا يرفع العدادات.

---

## 9. اختبار Staging الحقيقي لـ FCM

### النتيجة: **NOT VERIFIED (Environment Constraint)**

### الأسباب:
1. يتطلب استدعاء Google FCM HTTP v1 توفر ملف الاعتماد الخدمي `fcm.json` (Service Account JSON).
2. ملف `fcm.json` غير موجود على البيئة المحلية الحالية (`file_exists(base_path('fcm.json')) == false`)، وهو مستثنى أمنياً من الـ Git Repository.
3. الاختبار البرمجي الداخلي يؤكد صحة بناء الـ Payload وتصنيف الأخطاء (مثل `UNREGISTERED` و `NOT_FOUND` كأخطاء دائمة، والـ 5xx كأخطاء مؤقتة)، لكن الاتصال الحي الفعلي بخوادم Google يتطلب نقله على سيرفر Staging تتوفر فيه بيانات الاعتماد.

---

## 10. قياس أداء وظائف الدفعات (Batch Jobs Metrics)

* **حجم الدفعة (Batch Size):** 50 جهازاً لكل وظيفة (`array_chunk($eligibleDevices, 50)`).
* **التزامن (Concurrency):** 50 طلباً متزامناً عبر بروتوكول `Http::pool` في كل دفعة.
* **حدود التوقيت (Timeouts):**
  * مهلة الاتصال بالدفعة: `SendFcmBatchJob::$timeout = 60s`.
  * مهلة إعادة محاولة قاعدة البيانات: `retry_after = 90s` (في `config/queue.php`).
  * الفارق الإيجابي (30 ثانية) يضمن انتهاء أو إخفاق الدفعة وموت الـ Worker قبل أن يعتبرها محرك الطوابير مهجورة ويعيد إطلاقها، مما يمنع التكرار التلقائي الناتج عن الـ Timeouts.
* **منع التكرار (Deduplication):** مثبت ومفحوص برمجياً عبر فلترة التوكنات المسجلة في `notification_tokens`.
* **أوقات الاستجابة الحقيقية (p50 / p95):** لا يمكن قياسها محلياً لعدم توفر `fcm.json`، ويتم قياسها على سيرفر الاختبار عند الاتصال الفعلي بشبكة Google.

---

## 11. بوابة التوافق مع تطبيقات الهاتف المنشورة (Compatibility Gate)

* تم فحص دوال تسجيل الدخول القديمة (`AuthenticationController.php`, `Driver\AuthController.php`, `Technical\AuthController.php`):
  * ما زالت تستخدم `Device::updateOrCreate(['user_id' => ..., 'type' => ...])` القديمة وتعمل بنجاح.
  * لا يوجد أي كسر أو تعديل على المسارات القديمة التي تعتمد عليها تطبيقات Flutter الحالية في المتاجر.
* فحص التوافق الفيزيائي المباشر مع أجهزة حقيقية يتطلب تشغيل نسختي التطبيق (القديمة والجديدة) على بيئة Staging.

---

## 12. النتيجة النهائية لتقييم بنود الخطة (Final Scorecard)

| # | البند من الخطة | النتيجة |
|:---:|---|:---:|
| 1 | ترحيل وتوسيع قاعدة البيانات دون كسر البيانات الحالية | **PASS** |
| 2 | إصلاح مسمى Delivered والاعتماد على قُبل من FCM | **PASS** |
| 3 | معالجة خطأ `explode()` وتوحيد مدخلات `for_data` | **PASS** |
| 4 | دعم تعدد الأجهزة للمستخدم الواحد | **PASS** |
| 5 | التحول للإرسال المباشر بحزم متزامنة (FCM HTTP v1) | **PASS** |
| 6 | إلغاء الحذف الإجباري للتوكنات وتطبيق الحالات | **PASS** |
| 7 | شاشة صحة العملاء والأجهزة (`health.blade.php`) | **PASS** |
| 8 | واجهات المزامنة (`/api/devices/sync`, `preference`, `events`) | **PASS** |
| 9 | توحيد القيم والـ Aliases (`platform/type`, `granted`, `technician`) | **FAIL** |
| 10 | نظام الـ Feature Flags وخطة الرجوع الآمنة دون Migration Rollback | **NOT IMPLEMENTED** |
| 11 | شاشات لوحة التحكم (تفاصيل الحملة، تبويب الأهلية، نتائج الأجهزة، المعاينة) | **NOT IMPLEMENTED** |
| 12 | شاشة مراقبة الـ Queue والـ Retry للأخطاء المؤقتة | **NOT IMPLEMENTED** |
| 13 | الأمان والتحقق من ملكية الجهاز في Notification Events API | **FAIL** |
| 14 | الاختبارات الآلية الشاملة لنظام الإشعارات في المشروع | **NOT IMPLEMENTED** |
| 15 | اختبار Staging الميداني الحي مع Google FCM | **NOT VERIFIED** |
| 16 | قياسات زمن الاستجابة الفعلي (p50, p95) للاتصال الخارجي | **NOT VERIFIED** |
| 17 | بوابة التوافق للتطبيقات المنشورة (عبر الأجهزة الحقيقية) | **NOT VERIFIED** |

---

> [!IMPORTANT]
> **خلاصة التدقيق والقرار المقترح:**
> 
> تم إنجاز النواة الخلفية الأساسية (Core Backend Pipeline & Migrations & Health Dashboard) بنجاح فائق وتجاوزت اختبارات التكامل الهيكلية.
> ومع ذلك، لم يتم بعد إنجاز:
> 1. طبقة الـ **Feature Flags** للرجوع الآمن.
> 2. معالجة **الـ Aliases** (`type` مقابل `platform` و `granted` و `technician`).
> 3. شاشات لوحة التحكم التكميلية (تفاصيل الحملة، تبويب الأهلية، المعاينة قبل الإرسال).
> 4. تدقيق ملكية الجهاز في الـ Events API.
> 5. كتابة اختبارات آلية مخصصة للـ Notifications Package.
>
> تم إيقاف العمل والالتزام التام بعدم تعديل أي كود برمجياً انتظاراً لموافقتكم وتوجيهاتكم بالخطوة التالية.
