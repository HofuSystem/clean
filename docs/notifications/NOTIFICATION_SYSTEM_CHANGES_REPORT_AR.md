# تقرير شامل عن تحديثات وتطويرات نظام الإشعارات — CleanStation

**تاريخ التوثيق:** أكتوبر 2026  
**المشروع:** CleanStation Backend  
**النطاق:** نظام الإشعارات، مزامنة الأجهزة، وتتبع تفاعل العملاء (Notification & Device Tracking System)  
**الحالة:** مكتمل ومدمج محلياً وجاهز للإنتاج (Release Candidate Ready)  

---

## 1. ملخص تنفيذي (Executive Summary)

تمت ترقية وتطوير نظام الإشعارات في **CleanStation** من مجرد نظام إرسال تقليدي غير قابل للقياس إلى نظام متكامل عالي الدقة والأمان، يدعم:
1. **تتبع دقيق لمن فتح الإشعار وموعد الفتح (`opened_at`)** مع عدادات تراكمية للحملات.
2. **تتبع توقيت آخر استخدام أو فتح للعميل للتطبيق (`last_seen_at`)** عبر مزامنة الأجهزة وأحداث التفاعل.
3. **فحص ذكي لأهلية العملاء وأجهزتهم (Recipient Eligibility)** بناءً على حالة الحساب، تفضيلات التسويق، وأذونات الإشعارات.
4. **لوحة تحكم إدارية تفاعلية شاملة (Admin Dashboard)** تتيح معاينة الجمهور قبل الإرسال، مراقبة طوابير الإرسال، استعراض تفاصيل كل جهاز مقنع أمنياً، وتصدير البيانات عبر تدفق CSV فوري.
5. **حواجز أمان افتراضية (Feature Flags)** تضمن استمرار عمل النظام القديم بأمان حتى يتم تفعيل الميزات الحديثة برمجياً وبيئياً دون أي مخاطر.

---

## 2. الإجابة المباشرة: أين وكيف تتبع بيانات الإشعارات والعملاء؟

### أ. تتبع من فتح الإشعار وموعد الفتح (`opened_at` & `opened_count`)

* **في قاعدة البيانات:**
  * **لكل جهاز على حدة:** يُسجل في جدول `notification_tokens` في عمود **`opened_at`** (تاريخ ووقت الفتح الفعلي)، وتتحول حالة السجل إلى `status = 'opened'`.
  * **على مستوى الحملة ككل:** يتم تحديث العداد التراكمي في جدول `notifications` في عمود **`opened_count`**.
* **في الكود البرمجي (API):**
  * **الملف:** [`packages/core/notification/src/Controllers/Api/NotificationEventsController.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/Controllers/Api/NotificationEventsController.php)
  * **نقطة النهاية (Endpoint):** `POST /api/client/notifications/events`
  * **آلية المعالجة:**
    1. يتحقق النظام أولاً من تفعيل Feature Flag: `notification.delivery_events_enabled`.
    2. تتم المصادقة على العميل عبر توكن الحماية (`Sanctum / auth:api`).
    3. يتم التحقق الصارم من ملكية الجهاز (`device->user_id == user->id`) لمنع أي تلاعب أو انتحال.
    4. يتم تسجيل تاريخ الفتح داخل معاملة (Database Transaction) آمنة وغير قابلة للتكرار (Idempotent):
       ```php
       if (is_null($tokenRecord->opened_at)) {
           $tokenRecord->update([
               'opened_at' => $now,
               'status' => 'opened',
           ]);
           $notification->increment('opened_count');
       }
       $device->update(['last_notification_opened_at' => $now, 'last_seen_at' => $now]);
       ```

---

### ب. تتبع متى آخر مرة فتح العميل التطبيق (`last_seen_at`)

* **في قاعدة البيانات:**
  * يُسجل في جدول `devices` في عمود **`last_seen_at`**.
* **في الكود البرمجي (API):**
  يتم تحديث هذا التاريخ تلقائياً في سيناريوهين رئيسيين:
  1. **عند فتح التطبيق وبدء التشغيل (Device Sync):**
     * **الملف:** [`packages/core/users/src/Controllers/Api/DeviceSyncController.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/users/src/Controllers/Api/DeviceSyncController.php)
     * **نقطة النهاية (Endpoint):** `POST /api/devices/sync`
     * يقوم تطبيق الهاتف فور تشغيله بإرسال بيانات الجهاز وإصدار التطبيق والتوكن، فيقوم النظام بتحديث `last_seen_at = now()` وتحديث الرمز واستعادة الجهاز إذا كان محذوفاً ناعماً.
  2. **عند وصول أو فتح أي إشعار:**
     * **الملف:** [`packages/core/notification/src/Controllers/Api/NotificationEventsController.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/Controllers/Api/NotificationEventsController.php)
     * عند تسجيل حدث `received` أو `opened`، يقوم الكنترولر بتحديث `last_seen_at = now()` إلى جانب `last_notification_opened_at`.

---

### ج. حالة أجهزة العميل وأذونات الإشعارات (`notification_permission`)

* **في قاعدة البيانات (جدول `devices`):**
  * **`notification_permission`**: حالة إذن الإشعارات بنظام التشغيل (`authorized` مسموح، `denied` محظور، `provisional` مؤقت، `not_determined` غير محدد).
  * **`permission_checked_at`**: وقت آخر فحص لحالة الإذن على الجهاز.
  * **`token_status`**: حالة التوكن (`active` نشط، `invalid` تالف، `expired` منتهي).
  * **`app_version`**: إصدار تطبيق كلين ستيشن المثبت على جهاز العميل.
  * **`os_version`**: إصدار نظام التشغيل (iOS / Android / HarmonyOS).
  * **`device_model`**: موديل جهاز العميل (مثل: iPhone 15 Pro, Samsung S24).

---

## 3. التعديلات الهيكلية في قاعدة البيانات (Database Migrations)

تمت إضافة 5 ملفات هجرة بيانات تراكمية مؤمنة كلياً بفهارس وقيم افتراضية تمنع أي تأثير سلبي على الأداء أو كسر للبيانات القديمة:

| اسم ملف الـ Migration | الجدول المتأثر | الأعمدة الجديدة المضافة والغرض منها |
|---|---|---|
| `2026_09_26_180001_add_device_management_columns_to_devices_table.php` | `devices` | `installation_id`, `app_context`, `token_status`, `notification_permission`, `permission_checked_at`, `token_refreshed_at`, **`last_seen_at`**, `last_notification_received_at`, **`last_notification_opened_at`**, `app_version`, `os_version`, `device_model`, `invalidated_at`, `invalid_reason`. |
| `2026_09_26_180002_add_is_allow_notify_confirmed_at_to_users_table.php` | `users` | `is_allow_notify_confirmed_at` (توثيق تاريخ ووقت موافقة العميل على استقبال الإعلانات التسويقية). |
| `2026_09_26_180003_add_campaign_tracking_columns_to_notifications_table.php` | `notifications` | `purpose` (تسويقي / تشغيلي / نظامي)، `delivery_channel` (مباشر Direct أو Legacy Topic)، `targeted_users_count`، `eligible_users_count`، `eligible_devices_count`، `accepted_by_fcm_count`، `received_count`، **`opened_count`**، `permanent_failed_count`، `transient_failed_count`، `started_at`، `completed_at`. |
| `2026_09_26_180004_add_eligibility_columns_to_users_notifications_table.php` | `users_notifications` | `eligibility_status` (مؤهل، معطل التسويق، مرفوض الصلاحية، لا يملك جهاز...)، `eligibility_reason`، `accepted_devices_count`، `failed_devices_count`، مع الحفاظ التام على العزل المورفي لـ `BannerNotification`. |
| `2026_09_26_180005_expand_notification_tokens_table.php` | `notification_tokens` | `device_id`، `installation_id`، `platform`، `status`، `received_at`، **`opened_at`**، `fcm_message_id`، `error_code`، `error_message`. |

---

## 4. قائمة الملفات المعدلة والجديدة في المشروع ومساراتها

### أ. ملفات واجهة البرمجة والمنطق الخلفي (Backend & Logic):

1. **[`packages/core/users/src/Controllers/Api/DeviceSyncController.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/users/src/Controllers/Api/DeviceSyncController.php):**
   * استقبال مزامنة الأجهزة عند فتح التطبيق.
   * تسوية البيانات وتوحيد المصطلحات (Normalization) للتوافق مع التطبيقات القديمة والجديدة.
   * تحديث **`last_seen_at`** وتحديث بيانات الرمز واستعادة الأجهزة المحذوفة ناعماً.

2. **[`packages/core/notification/src/Controllers/Api/NotificationEventsController.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/Controllers/Api/NotificationEventsController.php):**
   * استقبال أحداث الوصول (`received`) والفتح (`opened`).
   * إغلاق الثغرات والتحقق الصارم من ملكية الجهاز.
   * تسجيل تاريخ الفتح **`opened_at`** وتحديث عداد الحملة التراكمي **`opened_count`** وتحديث **`last_seen_at`**.

3. **[`packages/core/notification/src/Services/RecipientEligibilityService.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/Services/RecipientEligibilityService.php):**
   * محرك فحص أهلية العملاء والأجهزة قبل الإرسال.
   * دعم وضعي الرصد (`observe`) والتطبيق الفعلي (`enforce`).
   * ميزة **المعاينة الذكية للجمهور (`previewAudience`)** لفحص المستهدفين قبل بدء الحملة.

4. **[`packages/core/notification/src/Helpers/NotificationDataNormalizer.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/Helpers/NotificationDataNormalizer.php):**
   * دالة `maskToken`: تقوم بتقنيع رموز الأجهزة أمنياً (`***` مع آخر 6 أحرف فقط مثل `***abc123`) لمنع تسريب التوكن الخام في شاشات الإدارة أو ملفات التصدير.
   * دعم صيغ متعددة للمستهدفين (JSON, Array, CSV, ID, Null).

5. **[`config/notification.php`](file:///d:/programming/projects/Hofu/CleanStation/config/notification.php):**
   * ملف الإعدادات المركزي للتحكم بميزات الإشعارات عبر Feature Flags آمنة.

---

### ب. شاشات لوحة التحكم الإدارية (Dashboard Controllers & Blade Views):

1. **[`packages/core/notification/src/Controllers/Dashboard/NotificationsController.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/Controllers/Dashboard/NotificationsController.php):**
   * ترقية دالة `show($id)` لتقديم تفاصيل شاملة للحملة ومؤشرات الأداء.
   * دالة `getEligibilityUsers`: جدول أهلية المستخدمين Server-side مع البحث والتصفية.
   * دالة `getDeviceResults`: جدول نتائج الأجهزة Server-side مع تقنيع الرموز.
   * دالة `queueMonitor`: شاشة مراقبة دفعات وطوابير الإشعارات.
   * دالة `previewAudience`: واجهة AJAX لمعاينة أرقام ونسب الجمهور قبل الحفظ.
   * دالة `exportDetails`: تصدير متدفق عالي السرعة لملفات CSV (الأهلية، الأجهزة، أخطاء التسليم).
   * دالة `retryTransient`: إعادة محاولة آمنة ومجدولة للأجهزة التي واجهت فشلاً مؤقتاً فقط.

2. **قوالب الواجهات (Blade Views):**
   * **[`packages/core/notification/src/resources/views/pages/notifications/show.blade.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/resources/views/pages/notifications/show.blade.php):**
     شاشة تفاصيل الإشعار المتكاملة المقسمة إلى تبويبات:
     * **تبويب الملخص:** القناة، الغرض، حالة المعالجة، والعدادات الرئيسية.
     * **تبويب أهلية المستخدمين (User Eligibility):** من استهدف، ولماذا قبل أو استبعد.
     * **تبويب نتائج الأجهزة (Device Results):** المنصة، حالة التوكن المقنع، وقت الوصول، ووقت الفتح.
     * **تبويب التفاعل (Engagement):** إحصائيات الاستلام والفتح، مع عرض "غير متاح" لحملات Topic التاريخية منعاً للتضليل.
     * **أزرار التصدير وإعادة المحاولة:** تنزيل تقارير CSV وإعادة محاولة الفشل المؤقت.
   * **[`packages/core/notification/src/resources/views/pages/notifications/queue-monitor.blade.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/resources/views/pages/notifications/queue-monitor.blade.php):**
     شاشة مراقبة طوابير الإشعارات، الدفعات المعلقة، أقدم دفعة، وآخر خطأ آمن.
   * **[`packages/core/notification/src/resources/views/pages/notifications/edit.blade.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/resources/views/pages/notifications/edit.blade.php):**
     شاشة إنشاء/تعديل الإشعار مع زر **"معاينة الجمهور (Audience Preview)"** التفاعلي.
   * **[`packages/core/notification/src/resources/views/pages/notifications/list.blade.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/resources/views/pages/notifications/list.blade.php):**
     إضافة زر وصول سريع لشاشة مراقبة الطوابير في شريط الأدوات.

---

### ج. المسارات (Routes):

* **مسارات لوحة الإدارة ([`packages/core/notification/src/routes/dashboard.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/routes/dashboard.php)):**
  * `GET  /admin/notifications/queue-monitor` -> مراقبة الطوابير.
  * `POST /admin/notifications/preview-audience` -> معاينة الجمهور.
  * `GET  /admin/notifications/{id}` -> عرض تفاصيل الحملة وتبويباتها.
  * `POST /admin/notifications/{id}/getEligibilityUsers` -> جلب بيانات جدول الأهلية.
  * `POST /admin/notifications/{id}/getDeviceResults` -> جلب بيانات جدول الأجهزة.
  * `POST /admin/notifications/{id}/retry-transient` -> إعادة محاولة الأخطاء المؤقتة.
  * `GET  /admin/notifications/{id}/export/{type}` -> تصدير ملفات CSV (`eligibility`, `devices`, `errors`).

* **مسارات الـ API ([`packages/core/users/src/routes/api.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/users/src/routes/api.php) & [`packages/core/notification/src/routes/api.php`](file:///d:/programming/projects/Hofu/CleanStation/packages/core/notification/src/routes/api.php)):**
  * `POST /api/devices/sync` -> مزامنة الأجهزة وتحديث `last_seen_at`.
  * `POST /api/client/notifications/events` -> تسجيل أحداث الاستلام والفتح وتحديث `opened_at`.

---

## 5. دليل استخدام شاشات لوحة التحكم (How to Access & Inspect)

### 1. استعراض تفاصيل حملة إشعار ومن فتح الإشعار:
1. افتح لوحة التحكم وتوجه إلى قائمة الإشعارات، ثم انقر على **"عرض التفاصيل"** لأي إشعار، أو افتح الرابط:
   `/admin/notifications/{id}`
2. **تبويب أهلية المستخدمين (User Eligibility):**
   * يمكنك التصفية حسب الحالة: (مؤهل، لا يملك جهاز، معطل التسويق، مرفوض الصلاحية، لا يوجد توكن صالح، غير نشط).
   * يمكنك البحث برقم هاتف أو اسم العميل لمعرفة سبب أهليته وعدد أجهزته.
3. **تبويب نتائج الأجهزة (Device Results):**
   * يعرض كل جهاز تم إرسال الإشعار له.
   * يوضح منصة الجهاز (iOS / Android / Huawei)، نسخته، والتوكن المقنع.
   * يوضح عمود **`Opened At`** بدقة متى فتح هذا الجهاز الإشعار.
4. **تبويب التفاعل (Engagement):**
   * يعرض مؤشرات نسبة الوصول ونسبة الفتح للحملات المباشرة.

### 2. معاينة الجمهور قبل الإرسال (Audience Preview):
1. عند إنشاء إشعار جديد في `/admin/notifications/create`.
2. بعد تحديد معايير الاستهداف والغرض، انقر على زر **"معاينة الجمهور"**.
3. ستظهر نافذة فورية توضح:
   * إجمالي العملاء المستهدفين.
   * عدد العملاء المؤهلين للإرسال.
   * عدد ونسب الأجهزة المقبولة والموزعة حسب المنصة (iOS مقابل Android).
   * تحذير ذكي باللون الأصفر إذا كانت نسبة الأجهزة المؤهلة أقل من 50%.

### 3. تصدير التقارير التفصيلية (CSV Exports):
* من صفحة تفاصيل أي إشعار، تتوفر ثلاثة أزرار تصدير متدفقة تستهلك الحد الأدنى من الذاكرة:
  1. **تصدير أهلية المستخدمين (Eligible Users CSV):** قائمة العملاء، الحالات، وأسباب الأهلية.
  2. **تصدير نتائج الأجهزة (Device Results CSV):** تفاصيل كل جهاز، حالات الوصول، وتوقيت الفتح.
  3. **تصدير أخطاء التسليم (Delivery Errors CSV):** تفاصيل أي أجهزة واجهت أخطاء وكود الخطأ ورسالته الآمنة.

---

## 6. مصفوفة مفاتيح التحكم والأمان (Feature Flags Configuration)

تم توحيد كافة المفاتيح في الملف المركزي [`config/notification.php`](file:///d:/programming/projects/Hofu/CleanStation/config/notification.php) لضمان العمل بسلامة مطلقة:

```php
return [
    // 1. استقبال وتحديث أجهزة وتواجد العملاء (مفعل افتراضياً)
    'device_sync_enabled' => env('NOTIFICATION_DEVICE_SYNC_ENABLED', true),

    // 2. الإرسال المباشر FCM (معطل افتراضياً ويعتمد المسار القديم الآمن Legacy Topic)
    'direct_fcm_enabled' => env('NOTIFICATION_DIRECT_FCM_ENABLED', false),

    // 3. وضع رصد الصلاحيات وتفضيلات التسويق (يرصد ويحصي بدون حجب قسري في الوضع الحالي)
    'permission_mode' => env('NOTIFICATION_PERMISSION_MODE', 'observe'),
    'marketing_preference_mode' => env('NOTIFICATION_MARKETING_PREFERENCE_MODE', 'observe'),

    // 4. تسجيل أحداث الوصول والفتح من الهواتف
    'delivery_events_enabled' => env('NOTIFICATION_DELIVERY_EVENTS_ENABLED', false),

    // 5. تفعيل الإحصائيات والشاشات الحديثة في لوحة التحكم (مفعل افتراضياً)
    'new_dashboard_metrics_enabled' => env('NOTIFICATION_NEW_DASHBOARD_METRICS_ENABLED', true),
];
```

### توصيات التفعيل المستقبلي في ملف `.env` للإنتاج:
* **لتفعيل تتبع أحداث الفتح والاستلام:** اضبط `NOTIFICATION_DELIVERY_EVENTS_ENABLED=true` عند اكتمال جاهزية ربط الحدث في تطبيق Flutter.
* **لتطبيق الحجب الفعلي لمن رفض التسويق أو رفض الصلاحية:** اضبط `NOTIFICATION_MARKETING_PREFERENCE_MODE=enforce` و `NOTIFICATION_PERMISSION_MODE=enforce`.

## ملحق مراجعة الكود والإصلاحات اللاحقة — 12 أكتوبر 2026

هذا الملحق يحدّث البنود المتأثرة وفق الكود الحالي وتقارير الإغلاق اللاحقة. تبقى نتائج الاختبار والتشغيل الميداني في الأقسام السابقة سجلاً تاريخياً، ولا تعني وحدها أن الحالة الحالية مطابقة لها.

### إصلاحات أُنجزت

1. **منع الاستيلاء على سجل جهاز:** في `DeviceSyncController::sync`، إذا طابق `installation_id` أو رمز FCM سجلاً مرتبطاً بمستخدم آخر، تُرفض المزامنة برمز `409`. يجب على الحساب السابق فصل الجهاز عبر مسار تسجيل الخروج قبل ربطه بحساب آخر. معرّفات الجهاز والرموز التي يرسلها التطبيق لا تُعامل كإثبات ملكية.
2. **تسجيل أحداث متزامن وآمن من التكرار:** تحديث `received_at` و`opened_at` أصبح شرطياً وذرياً على مستوى قاعدة البيانات؛ لا يزيد عداد الحملة إلا إذا غيّر الطلب الصف فعلاً. هذا يغطي وصول نسخ متزامنة من الحدث.
3. **Kill Switch:** الإصلاح الموثق في تقارير المرحلتين 2.1 و2.2 يحدّث سجلات دفعة الأجهزة إلى `skipped_by_feature_flag` مع مطابقة دقيقة، ويغلق بأمان عند غياب المعرّفات.
4. **صلاحية تصدير التفاصيل:** مراجعة `CheckPermissions` الحالية تُلزم تصريح `export` لمسار `exportDetails`، ولا تمنح صلاحية العرض بديلاً عنه.

### دلالات القياس والتهيئة

- `accepted_by_fcm_count` يعني أن خادم FCM قبل الطلب، ولا يثبت وصوله إلى الجهاز. `received_count` و`opened_count` مصدرهما أحداث التطبيق، لذا فهما مؤشرا تفاعل يرسله العميل وليسا إثباتاً مستقلاً أو محمياً من عميل معدل.
- الإعدادان `marketing_preference_mode` و`permission_mode` افتراضياً `observe`. في هذا الوضع تُسجل حالة الانسحاب أو رفض الإذن للمراقبة ولا تمنع الإرسال. تفعيل المنع يتطلب ضبطهما إلى `enforce` بعد التحقق من توافق سياسة المنتج مع التطبيقات المنشورة.
- مزامنة الجهاز تقبل المنصات `ios` و`android` و`huawei`، كما تقبل `type` بديلاً عن `platform`. يُحوّل `granted` إلى `authorized`، و`technician` إلى `technical`. يجب على فرق التطبيقات الالتزام بهذه القيم؛ لا تُقبل أسماء المنصات الأخرى حالياً.
- بيانات الأجهزة تتضمن معرّفات ورموزاً وبيانات تقنية. يستمر إخفاء رمز FCM في واجهات التصدير؛ يوصى بتقييد صلاحية التصدير ومراجعة الاحتفاظ بهذه البيانات وفق الحاجة التشغيلية.

### نقاط متابعة متبقية

- يلزم إضافة تغطية آلية مخصصة لمسار مزامنة الجهاز وأحداث الإشعار، خصوصاً محاولة ربط جهاز مملوك لمستخدم آخر، وطلبات الأحداث المكررة والمتزامنة، وعدم خفض الحالة `opened` إلى `received`.
- لا يوجد قيد فريد على `installation_id` في مخطط الأجهزة الحالي. بعد فحص السجلات القديمة ومعالجة أي تكرارات، يوصى بإضافة قيد فريد مناسب (مع مراعاة القيم الفارغة ومحرك قاعدة البيانات) لمنع إنشاء سجلين لنفس التثبيت في سباق طلبات متزامنة.
- ينبغي عرض أحداث `received/opened` في لوحة الإدارة مع توضيح أنها تقارير عميل، والفصل بينها بصرياً عن قبول FCM.
- نتائج Staging وFCM الحقيقي، توافق نسخ التطبيقات المنشورة، وتشغيل الاختبارات يجب إعادة التحقق منها في بيئتها؛ مراجعة الكود لا تثبت نجاح اتصال Google أو توافق العميل الفعلي.

## ملحق استكمال نقاط المتابعة — 12 أكتوبر 2026

- أضيفت حماية على مستوى قاعدة البيانات: Migration `2026_10_12_120000_add_unique_indexes_to_devices_identifiers.php` يفرض فرادة `installation_id` غير الفارغ. قبل إنشاء الفهرس يفحص التكرارات ويتوقف برسالة واضحة بدلاً من حذف أو دمج سجلات قد تختلف ملكيتها. يجب تنفيذ migration على نسخة من بيانات الإنتاج أولاً؛ إذا ظهر تكرار، يلزم حسم السجلات يدوياً مع الحفاظ على الملكية ثم إعادة التنفيذ. يبقى `device_token` غير فريد لأجل توافق تدفقات التطبيق القديمة.
- أضيفت اختبارات تغطي محاولة الاستيلاء على `installation_id`، وتعارض رمز FCM أجنبي مع تثبيت يملكه المستخدم الحالي، ووصول حدث `received` بعد تسجيل `opened`.
- عُدلت عبارات لوحة الحملة لتقول «أبلغ التطبيق» بدلاً من الجزم بأن الجهاز استلم أو فتح فعلياً، مع تنبيه يوضح مصدر القياس وحدوده.
- لم يتسن تشغيل الاختبارات أو فحص صياغة PHP في هذه البيئة لعدم توفر PHP CLI. كذلك لا تتوفر قاعدة الإنتاج هنا لفحص تكرارات `installation_id` أو تنفيذ migration عليها. قبل النشر، شغّل الاختبارات على قاعدة `testing.sqlite` المعزولة، ثم نفذ فحص التكرارات وطبّق migration على نسخة Staging.
- لم يُغيّر الوضع الافتراضي `observe` إلى `enforce`: التحويل يغير سياسة إرسال التسويق والإشعارات للمستخدمين الحاليين، ويحتاج قراراً تشغيلياً وتأكيد توافق نسخ التطبيقات. عند اعتماد الحجب، اضبط `NOTIFICATION_MARKETING_PREFERENCE_MODE` و`NOTIFICATION_PERMISSION_MODE` صراحة إلى `enforce`.
