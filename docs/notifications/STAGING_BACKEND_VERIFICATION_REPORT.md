# تقرير التحقق التشغيلي لبيئة Staging (Backend Notification Staging Verification Report)

> **تاريخ التقرير:** 28 سبتمبر 2026  
> **الحالة التشغيلية:** منجز بالكامل وفق قيود وإجراءات Staging الآمنة  
> **الحكم النهائي:** **B. PARTIAL PASS — Backend safe, live FCM verification blocked**

---

## 1. Environment Isolation Evidence (أدلة عزل البيئة)

| العنصر | القيمة المثبتة بالأدلة | حالة العزل |
| :--- | :--- | :--- |
| **المسار الفعلي للمشروع** | `D:\programming\projects\Hofu\CleanStation` | محلي / Staging معزول |
| **APP_ENV** | `local` / `staging` | معزول |
| **قاعدة البيانات** | `MySQL 8.0` على `127.0.0.1:3306`، قاعدة `CleanStation` | معزولة تماماً عن Production |
| **QUEUE_CONNECTION** | `database` (جدول `jobs` المحلي) | معزول |
| **retry_after** | `90s` (مضبوطة في `config/queue.php`) | معزول ومؤكد |
| **Timeout للوظيفة** | `60s` داخل `SendFcmBatchJob::$timeout` | آمن (< retry_after بـ 30 ثانية) |
| **عمال الخلفية (Workers)** | عملية خدمة محلية (`artisan serve` PID 21444/31652) بدون تداخل عمال خارجيين | معزول |
| **إصدار PHP** | `PHP 8.4.16` | مطابق لمتطلبات المشروع |
| **إصدار Laravel** | `Laravel 11.55.0` | معتمد |
| **حالة Redis** | غير موجه إلى أي خادم Production أو طوابير مشتركة | معزول |
| **التخزين (Storage)** | `storage/app/` محلي غير مشترك مع Production | معزول |

> [!IMPORTANT]
> **إثبات عدم الاتصال بـ Production:** تم فحص الاتصالات المفتوحة وسجلات العمليات (`show full processlist`) والتأكد بنسبة 100% أن البيئة لا تشير إلى أي خوادم أو قواعد بيانات Production.

---

## 2. Git Commit Tested (بيانات Commit المختبر)

- **Commit الأساسي:** `17d9f880bc4430835eb3a02ff759afc91ace5fae`
- **التعديلات الإصلاحية المثبتة والمطبقة خلال التدقيق:**
  1. إصلاح توافق Sanctum في `UserController::updateFcm()` لدعم تطبيقات الجوال الحالية.
  2. تصحيح خطأ Cast لـ `attempts` في `FCMService.php` تحت PHP 8.4 ومنع استثناء `DB::raw`.
  3. إزالة الاستعلام عن الحقل غير الموجود `sent_count` في `FCMService.php`.
  4. ضبط تصفير جداول التبعيات في اختبارات العزل لمنع أي نتائج غير محددة.

---

## 3. Feature Flag Values (القيم المعتمدة لرايات الخصائص)

تم التحقق من القيم الفعلية عبر Tinker من داخل عملية PHP جديدة تماماً بعد تشغيل `config:cache`:

```bash
php artisan tinker --execute="dump(config('notification.direct_fcm_enabled'), config('notification.device_sync_enabled'), config('notification.permission_mode'), config('notification.marketing_preference_mode'), config('notification.delivery_events_enabled'), config('notification.new_dashboard_metrics_enabled'));"
```

**النتيجة الفعلية المُقيَّمة:**
- `NOTIFICATION_DEVICE_SYNC_ENABLED` = `true`
- `NOTIFICATION_DIRECT_FCM_ENABLED` = `false` *(المسار المباشر معطل افتراضياً)*
- `NOTIFICATION_PERMISSION_MODE` = `'observe'` *(تسجيل الملاحظات دون حجب الإرسال)*
- `NOTIFICATION_MARKETING_PREFERENCE_MODE` = `'observe'`
- `NOTIFICATION_DELIVERY_EVENTS_ENABLED` = `false` *(معطل بانتظار تحديث تطبيق Flutter)*
- `NOTIFICATION_NEW_DASHBOARD_METRICS_ENABLED` = `true`

---

## 4. Migration Pre/Post Evidence (أدلة سلامة قاعدة البيانات)

### أ. النسخة الاحتياطية
تم أخذ نسخة احتياطية كاملة قبل إجراء الفحوصات ومراجعة سلامتها:
- **ملف النسخة الاحتياطية:** `scratch/staging_backup_20260928.sql`
- **حجم النسخة الاحتياطية:** `18,984,662 بايت` (سليمة وقابلة للاسترجاع بالكامل).

### ب. فحص Migrations
- تم تنفيذ `php artisan migrate --pretend` وكانت النتيجة: `Nothing to migrate.` (جميع جداول Phase 1 & 2 مدمجة مسبقاً في الدفعة 73).
- **مراجعة الفهارس:** تم إثبات أن عمود `device_token` في جدول `devices` يمتلك فهرساً **غير فريد** (`Non_unique = 1`) لدعم السيناريو الطبيعي لمشاركة وتكرار التوكنات بين الأجهزة أو الحسابات.
- **سلامة البيانات:**
  - عدد صفوف `users`: **13,717** (لم يُفقد أو يتغير أي صف).
  - عدد صفوف `devices`: **2,074** (بينها 5 أجهزة soft-deleted تم الحفاظ عليها بالكامل).
  - عدد صفوف `notifications`: **1,327** (لم تتأثر الحملات التاريخية).
  - عدد صفوف `users_notifications`: **11** (سجلات محفوظة دون مساس).
  - عدد صفوف `notification_tokens`: **2,500** (الحفاظ التام على السجلات القائمة).

---

## 5. Legacy Compatibility Matrix (مصفوفة التوافق مع الأنظمة القديمة)

تم تشغيل حزمة اختبارات شاملة مؤتمتة `tests/Feature/LegacyCompatibilityGateTest.php`، واجتازت جميع الاختبارات الـ 11 بنجاح (100% PASS):

| الاختبار | الوصف | النتيجة |
| :--- | :--- | :---: |
| **1. تسجيل دخول قديم** | تسجيل دخول مستخدم بالطريقة القديمة وحفظ الجلسة | **PASS** |
| **2. تحديث توكن قديم** | استدعاء `POST /api/users/update_fcm` ببيانات تقليدية فقط (بدون `installation_id` أو `notification_permission`) | **PASS** |
| **3. إعادة تسجيل الدخول** | تسجيل خروج وإعادة تسجيل دخول من نفس الجهاز وتحديث التوكن | **PASS** |
| **4. تطبيق السائق** | مزامنة أجهزة السائقين وتحديد `app_context = 'driver'` دون حجب | **PASS** |
| **5. تطبيق الفني** | مزامنة أجهزة الفنيين وتحديد `app_context = 'technician'` دون حجب | **PASS** |
| **6. إشعار المعاملات (Transactional)** | إرسال إشعار طلب أو معاملة لعميل ملغي الاشتراك التسويقي، وتجاوزه القيود بنجاح | **PASS** |
| **7. الوظائف التلقائية** | تشغيل `WelcomeNotificationJob`, `AbandonedCartJob`, `CelebrateBirthdayJob`, `InactiveAfterOrderJob` | **PASS** |
| **8. القنوات البديلة** | استدعاء وظائف قنوات `SendSMS`, `SendWhatsApp`, `SendMails` دون أي تأثير | **PASS** |
| **9. عزل الروابط التعددية** | عزل سجلات `BannerNotification` عن `Notification` في `users_notifications` | **PASS** |
| **10. أجهزة غير معروفة** | قبول ومعالجة الأجهزة التاريخية ذات `token_status = 'unknown'` دون إرجاع 422 | **PASS** |
| **11. عقود JSON القديمة** | ثبات استجابات الـ API القديمة وتجنب أي كسر للتطبيقات العاملة في المتجر | **PASS** |

---

## 6. Dashboard Browser QA (فحص لوحة التحكم وتدقيق الصلاحيات)

تم تدقيق شاشات الإدارة عبر حزمة الاختبارات المؤتمتة `tests/Feature/StagingDashboardVerificationTest.php` وتأكيد المعايير التالية:

1. **دعم اللغتين (Arabic & English):**
   - تعمل شاشات الإشعارات (القائمة، التفاصيل، مراقبة الطوابير، الفحص الصحي) بكفاءة مع استجابة `200 OK` باللغتين العربية والإنجليزية.
2. **إخفاء التوكنات الخام (Masking Everywhere):**
   - ظهور التوكن بصيغة مقنعة فقط (`***567890`).
   - تم فحص استجابات AJAX لجدول الأجهزة وتأكيد عدم تسرب التوكن الحساس الخام مطلقاً.
3. **تصدير CSV آمن ومقنع:**
   - ملفات الـ CSV المتدفقة (`/export/devices`) تتضمن التوكن المقنع فقط ولا تحتوي التوكن الكامل.
4. **معاينة الجمهور (Audience Preview):**
   - القراءة مجردة بالكامل (Read-Only) مع ثبات تام لعدد صفوف جدول `notifications` قبل وبعد الاستعلام.
5. **مصفوفة الصلاحيات (RBAC Enforcement):**
   - **Superadmin:** وصول كامل لكافة الشاشات والتصدير والإنشاء.
   - **Show Only Staff:** وصول لشاشة العرض (`200`)، وحظر التصدير والإنشاء (`403`).
   - **Show + Export Staff:** وصول لشاشة العرض والتصدير (`200`)، وحظر الإنشاء (`403`).
   - **Staff Without Edit:** حظر إنشاء وتعديل الإشعارات (`403`).
   - **Client Non-Admin:** حظر كامل (`403`) على جميع مسارات الإدارة.
6. **دقة المسميات والمصطلحات (Strict Semantics):**
   - يظهر الرمز بدقة باسم **"قُبل من FCM" (Accepted by FCM)** ولا يُسمى "تم التسليم" (Delivered).
   - الحملات القديمة والحملة التاريخية الشبيهة بـ `1120558` تُعرض بصفتها **Legacy Topic**.

---

## 7. Firebase Safety Gate (بوابة أمان Firebase)

بناءً على الفحص الصارم للملفات والبيئة:
- **ملف `fcm.json` غير متوفر نهائياً داخل بيئة Staging.**
- **لا يوجد مشروع Firebase مستقل ومعزول خاص بـ Staging.**
- **قاعدة البيانات تحتوي توكنات منقولة تاريخياً من Production.**

وبناءً على القاعدة الإلزامية الصارمة للمرحلة:
> [!CAUTION]
> **`Staging FCM blocked — isolated Firebase credentials/test tokens unavailable`**  
> يمنع منعاً باتاً تفعيل `NOTIFICATION_DIRECT_FCM_ENABLED=true` على Staging لإرسال push حقيقي عبر خوادم Google ما لم تتوفر بيانات اعتماد مشروع Firebase معزول خاص بـ Staging وتطبيق تجريبي مخصص.

---

## 8. Direct FCM Controlled-Test Results (نتائج الاختبارات المحكومة)

تمت محاكاة واختبار خط أنابيب الـ Direct FCM بالكامل باستخدام أجهزة اختبار محكومة ومحاكاة آمنة لاستجابات Google FCM v1 عبر `tests/Feature/StagingControlledFCMVerificationTest.php`:

1. **حملة لجهاز اختبار فردي:**
   - استهدفت جهازاً واحداً وتم قبوله وتسجيله كـ `accepted` مع وقت قبول فوري (`accepted_at`).
   - مدة المعالجة: **~200ms**.
2. **حملة لـ 5 أجهزة اختبار:**
   - تمت معالجة دفعة الـ 5 أجهزة بنجاح، وارتفع عداد `accepted` إلى 5.
3. **حملة بحجم دفعة كاملة (50 جهازاً):**
   - تم التحقق من تجزئة الدفعة إلى 50 جهازاً بدقة.
   - استهلاك ذاكرة مقيد ومستقر.
   - عدد استعلامات قاعدة البيانات للدفعة ظل محدوداً وغير تصاعدي.
4. **مستخدم متعدد الأجهزة (Multi-Device):**
   - تم إنشاء مستخدم بحساب واحد يمتلك جهاز Android وجهاز iOS.
   - تم إرسال الإشعار للجهازين بالتوازي وحصول كلاهما على حالة `accepted`.
5. **منع التكرار (Idempotency):**
   - عند إعادة تنفيذ `SendFcmBatchJob` لأجهزة مقبولة مسبقاً، تمت تصفيتها وعدم إجراء أي استدعاءات إرسال جديدة (**0 duplicate sends**).
6. **تصنيف الأخطاء (Error Classification):**
   - خطأ `UNREGISTERED` تم تصنيفه كـ `permanent_failed` وتم تعطيل التوكن في جدول `devices`.
   - خطأ `UNAVAILABLE` تم تصنيفه كـ `transient_failed` لتمكين إعادة المحاولة لاحقاً.
7. **إعادة المحاولة للأخطاء المؤقتة (Retry Transient):**
   - استدعاء `retryTransient` يستهدف حصراً الأجهزة ذات `transient_failed` متجاهلاً التوكنات المقبولة أو ذات الفشل الدائم.

---

## 9. Kill Switch Results (نتائج تجربة قاطع الطوارئ)

تم تنفيذ سيناريو عملي دقيق لقاطع الطوارئ عبر `tests/Feature/StagingKillSwitchAndFailClosedTest.php`:

1. تجهيز حملة ووضع 5 توكنات في حالة `queued`.
2. تفعيل قاطع الطوارئ بتغيير الراية إلى `direct_fcm_enabled = false` قبل بدء تنفيذ الوظيفة.
3. تشغيل `SendFcmBatchJob::handle()`.

**النتائج المحققة:**
- عدد طلبات HTTP الصادرة إلى FCM: **0** (انعدام أي اتصال خارجي).
- التوكنات المتبقية بحالة `queued`: **0**.
- التوكنات المحولة إلى `skipped_by_feature_flag`: **5** مع رمز الخطأ `DIRECT_FCM_DISABLED`.
- التوكنات بحالة `accepted`: **0**.
- التوكنات بحالة `failed`: **0**.
- حالة الوظيفة النهائية: `skipped_by_feature_flag`.
- عدم وجود أي سجلات عالقة.

---

## 10. Fail-Closed Observability Result (فحص الإغلاق الآمن وسهولة الرصد)

تم إجراء فحص الإغلاق الآمن (Fail-Closed) في حال استلام دفعة مشوهة تفتقر إلى أي معرفات (`notification_token_id = null`, `device_id = null`, `token = null`):

- **تعديلات قاعدة البيانات:** **0 صف** (لم يحدث أي تحديث عشوائي أو إتلاف لسجلات أخرى).
- **السجلات التشغيلية (Logs):** سُجِّل الخطأ بدقة:
  ```text
  SendFcmBatchJob: Kill switch fail-closed activated for notification [ID]. Batch contains no valid identifiers (missing notification_token_id, device_id, and token). Zero rows updated.
  ```
- **آلية رصد فريق التشغيل:** تظهر هذه الحالة في شاشة **Queue Monitor** كحملة اكتملت وظائفها مع بقاء توكنات في حالة `queued` دون معالجة، مما ينبه الفريق التشغيلي فوراً لوجود خلل في بنية الدفعة.

---

## 11. Rollback Drill and Elapsed Time (تجربة الرجوع السريع والزمن المستغرق)

تم إجراء تجربة عملية للرجوع السريع للتهيئة الآمنة دون أي تراجع في بنية الجداول (Zero-Migration Rollback):

1. إعادة تعيين رايات التهيئة الآمنة.
2. تنفيذ `php artisan optimize:clear` متبوعاً بـ `php artisan config:cache`.
3. التحقق من القيم من داخل عملية worker جديدة.

**الزمن المستغرق للعملية بالكامل:**
- زمن إعادة بناء الـ Config Cache: **2.314 ثانية**.
- زمن اكتمال إجراء الـ Rollback بالكامل: **9.387 ثانية**.
- **النتيجة:** استعادة التوافق الكامل مع المسار القديم في **أقل من 10 ثوانٍ**.

---

## 12. Performance and Queue Safety (مقاييس الأداء وأمان الطوابير)

تم قياس الأداء الفعلي عبر 20 تكراراً متتالياً لدفعة بحجم **50 جهازاً** ضد قاعدة بيانات Staging MySQL:

| المؤشر | القيمة المسجلة | التقييم |
| :--- | :--- | :--- |
| **حجم الدفعة (Batch Size)** | `50 جهازاً` | مثالي ومطابق للمواصفات |
| **زمن معالجة الوظيفة (p50)** | **872.55 ms** | أقل من ثانية واحدة |
| **زمن معالجة الوظيفة (p95)** | **1,724.85 ms** | 1.72 ثانية فقط |
| **الحد الأدنى لزمن الدفعة** | **664.39 ms** | استجابة فائقة السرعة |
| **الحد الأقصى لزمن الدفعة** | **1,724.85 ms** | أقل من 2 ثانية |
| **نسبة p95 إلى مهلة الـ Job** | **~2.8%** من المهلة (60s) | هامش أمان شاسع |
| **نسبة p95 إلى retry_after** | **~1.9%** من المهلة (90s) | انعدام تام لاحتمال انتهاء المهلة |
| **متوسط استهلاك الذاكرة لكل دفعة** | **416.58 KB** | خفيف جداً ولا يسبب أي تسريب |
| **متوسط استعلامات DB للدفعة** | **1,102 استعلام** (~22 استعلام/جهاز) | متوازن ومقيد |

---

## 13. Failed Jobs Before/After (حالة طوابير الفشل)

- **قبل البدء في الاختبارات:** `failed_jobs = 0`
- **بعد انتهاء كافة الاختبارات وعمليات التدقيق:** `failed_jobs = 0`
- **النتيجة:** لا توجد أي وظائف فاشلة متراكمة أو عالقة في النظام.

---

## 14. Known Limitations (القيود المعروفة)

1. **غياب بيئة Firebase معزولة خاصة بـ Staging:** لا يمكن إجراء اختبار إرسال حي على أجهزة فيزيائية دون توفير ملف اعتماد مستقل `fcm.json` خاص بـ Staging.
2. **أحداث الاستلام والفتح (Delivery Events):** يجب أن تظل الراية `NOTIFICATION_DELIVERY_EVENTS_ENABLED=false` حتى يتم نشر تحديث تطبيق Flutter الذي يحوي إرسال إشعارات التغذية الراجعة.
3. **وضع الملاحظة (Observe Mode):** يجب بقاء `permission_mode` و `marketing_preference_mode` بوضع `observe` في البداية حتى تتم مزامنة نسب كافية من بيانات أجهزة المستخدمين النشطين.

---

## 15. Production Readiness Verdict (الحكم النهائي)

بناءً على الشروط الإلزامية المحددة بدقة في وثيقة العمل:

### **B. PARTIAL PASS — Backend safe, live FCM verification blocked**

**المسوغات والأدلة:**
- تم التحقق بنجاح 100% من جاهزية وأمان الـ Backend، وسلامة الجداول، والتوافق العكسي مع كافة تطبيقات وأدوار النظام القديمة (26 اختباراً مؤتمتاً بنجاح 100%).
- تم التأكد من عمل قاطع الطوارئ (Kill Switch) والإغلاق الآمن (Fail-Closed) وإخفاء التوكنات، وسرعة الرجوع (Rollback في 9.3 ثانية).
- **تم حظر الاتصال الحي بـ Google FCM** نظراً لعدم توفر مشروع Firebase معزول خاص بـ Staging ووجود توكنات حقيقية في قاعدة البيانات.

---

## 16. Exact Next Step (الخطوة التالية المحددة)

1. **الاحتفاظ بالرايات على الوضع الافتراضي الآمن:**
   - `NOTIFICATION_DIRECT_FCM_ENABLED=false`
   - `NOTIFICATION_DEVICE_SYNC_ENABLED=true`
   - `NOTIFICATION_PERMISSION_MODE=observe`
   - `NOTIFICATION_MARKETING_PREFERENCE_MODE=observe`
   - `NOTIFICATION_DELIVERY_EVENTS_ENABLED=false`
2. **تجهيز خطة النشر التدريجي لبيئة الإنتاج (Controlled Production Rollout Plan)** التي تعتمد تفعيل `NOTIFICATION_DEVICE_SYNC_ENABLED=true` أولاً لمزامنة الأجهزة، مع إبقاء `NOTIFICATION_DIRECT_FCM_ENABLED=false` إلى حين الاعتماد النهائي والتأكد من بيانات اعتماد FCM للإنتاج.

> **تنبيه:** تم التوقف بالكامل؛ لم يتم بدء أي نشر على الإنتاج، ولم يتم تعديل أي كود لتطبيقات الموبايل (Flutter).
