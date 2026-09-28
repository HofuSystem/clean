# تقرير تدقيق قبول المرحلة الثانية: لوحة تحكم الإشعارات الإدارية
# PHASE 2 ACCEPTANCE AUDIT REPORT

**تاريخ التدقيق:** 27 سبتمبر 2026  
**المشروع:** Clean Station Backend  
**المرحلة المدققة:** Phase 2 (Admin Notification Dashboard Completion)  
**طبيعة التدقيق:** تدقيق صارم بالأدلة والأرقام (Read-Only / Empirical Evidence Audit)  
**النتيجة الإجمالية:** ⚠️ **FAIL (توقف إلزامي لوجود بندين بحاجة لإصلاح قبل الاعتماد النهائي)**  
**الوضع الميداني:** تم التوقف التام دون تعديل كود، ودون تفعيل Direct FCM، ودون بدء Staging أو Mobile.

---

## 1. جدول الحكم النهائي للتدقيق (Acceptance Audit Matrix)

| # | مجال التدقيق | النتيجة | السبب والبيان |
|---|--------------|:-------:|---------------|
| **1** | **دورة حياة Kill Switch وحالات الطابور** | ❌ **FAIL** | عند تفعيل الـ Kill Switch أثناء وجود وظائف في الطابور، تنتهي الوظائف دون تحديث جدول `notification_tokens`؛ فتبقى الأجهزة في حالة `queued` إلى الأبد وتصبح الحملة `completed`، ولا تسجل في `skipped_by_feature_flag` بقاعدة البيانات، وتظهر كأجهزة عالقة في Queue Monitor. |
| **2** | **مطابقة أعمدة Queue Monitor ومصادرها** | ✅ **PASS** | تم توثيق ورسم خريطة كل عمود، ومصدره من الجداول، ونوعه (Device count مقابل Campaign status و Batch timestamp). |
| **3** | **قياس أداء Audience Preview على قاعدة البيانات الكاملة** | ✅ **PASS** | تمت المعاينة على كافة مستخدمي النظام (13,663 مستخدماً)، وتم إثبات استقرار الذاكرة (Peak 52MB، دلتا 0-2MB) عبر Chunking (500 مستخدم)، ومطابقة محرك وقواعد الأهلية 100% مع قواعد الإرسال الفعلي. |
| **4** | **قياس أداء التصدير التفصيلي المتدفق (Exports)** | ✅ **PASS** | تم التصدير على 2,500 سجل؛ استجابة متدفقة StreamedResponse، دعم UTF-8 BOM، استهلاك ذاكرة ثابت (0MB دلتا)، صفر تكرار، وصفر تسريب لرموز الأجهزة (تقنيع التوكن مطبق 100%). |
| **5** | **الفحص البصري والواجهة (Browser / DOM QA)** | ✅ **PASS** | تم التحقق من كافة الأزرار، والتبويبات، ونوافذ الـ Modal، وظهور "غير متاح" للحملات القديمة، وتعطيل زر الـ Retry عند إيقاف الـ Flag. |
| **6** | **تدقيق الصلاحيات ومنع الاختراق (Authorization)** | ❌ **FAIL** | تسريب صلاحية التصدير (Permission Leak): السطر 78 في `CheckPermissions.php` يمنح صلاحية `exportDetails` لكل من يملك `show`، مما يسمح لمستخدم محجوب عنه التصدير بتحميل ملفات الـ CSV. |
| **7** | **تسوية ومطابقة نتائج الاختبارات (Test Reconciliation)** | ✅ **PASS** | استخراج القائمة الدقيقة لكافة الـ 13 اختباراً فاشلاً قديماً، وتفسير تغير عدد الـ Assertions وتأكيد عدم إسقاط أي اختبار منتج حقيقي. |

---

## 2. البند الأول: دورة حياة Kill Switch وحالات الطابور (Kill Switch Lifecycle)

### أ. سيناريو الاختبار المنفذ:
1. ضبط `direct_fcm_enabled = true`.
2. إنشاء حملة إشعار Direct (`targeted_users = 1`، `eligible_devices = 100`).
3. تجهيز 100 سجل في جدول `notification_tokens` بالحالة `status = 'queued'`.
4. جدولة دفعتين من وظيفة `SendFcmBatchJob` (كل دفعة 50 جهازاً) عبر `Bus::batch`.
5. تغيير قيمة الإعداد ديناميكياً قبل التنفيذ: `direct_fcm_enabled = false`.
6. تنفيذ الوظائف الموجودة في الطابور عبر استدعاء `handle()`.
7. فحص ومراقبة قاعدة البيانات وشاشة Queue Monitor.

### ب. النتائج الرقمية المقاسة بعد التنفيذ:

| الحقل / المقياس | القيمة المسجلة في DB | القيمة المعروضة في Queue Monitor | الحالة الدلالية |
|-----------------|:--------------------:|:--------------------------------:|-----------------|
| `notifications.processing_status` | `completed` | `مكتمل (Completed)` | الحملة انتهت |
| `notifications.completed_at` | `2026-09-27 13:23:00` | تاريخ ووقت الاكتمال | مسجل |
| `Batch finished()` | `true` | — | اكتمل الطابور |
| `notification_tokens.status = 'queued'` | **100** | **100 (في الطابور)** | ⚠️ **عالقة للأبد** |
| `notification_tokens.status = 'skipped_by_feature_flag'` | **0** | **0** | لم تسجل في DB |
| `notification_tokens.status = 'accepted'` | 0 | 0 | لم يتم اتصال FCM |
| `job->executionStatus` | `skipped_by_feature_flag` | غير مرئي للمستخدم | في ذاكرة الـ Job فقط |

### ج. إجابات الأسئلة الإلزامية:
1. **هل تبقى صفوف queued عالقة؟**
   - **نعم.** تبقى جميع السجلات الـ 100 في جدول `notification_tokens` بالحالة `queued` إلى الأبد، رغم خروج وظائف الـ Queue وانتهاء الـ Batch.
2. **أين تحفظ skipped_by_feature_flag بصورة دائمة؟**
   - **لا تحفظ في قاعدة البيانات إطلاقاً!** تحفظ فقط كخاصية في ذاكرة الـ PHP Object (`$this->executionStatus`) وتسجل في ملف الـ Log عبر `Log::warning(...)`.
3. **كيف تحسبها Queue Monitor؟**
   - تعتمد Queue Monitor على استعلام: `withCount(['notificationTokens as skipped_count' => fn($q) => $q->where('status', 'skipped_by_feature_flag')])`. ولأن السجلات في DB لم تُحدث، فإن العداد يعرض دائماً **0**!
4. **هل تصبح الحملة completed أم processing؟**
   - تصبح الحملة **`completed`** لأن خروج الـ Job دون استثناء يُعتبر اكتمالاً ناجحاً بالنسبة لـ `Bus::batch`، مما يؤدي إلى تفعيل دالة `->finally()` وتحديث حالة الحملة إلى `completed`. وهذا ينتج عنه تناقض خطير: الحملة مكتملة ولكن أجهزتها لا تزال تظهر كأنها في الطابور!
5. **هل Retry يستطيع التمييز بين skipped و transient_failed؟**
   - **لا.** لأن أجهزة الـ skipped بقيت بحالة `queued` وليست `skipped_by_feature_flag` ولا `transient_failed`، وبالتالي لن يلتقطها أمر إعادة المحاولة.

> 🔴 **النتيجة للبند الأول:** **FAIL**  
> **السبب الجذري (Root Cause):** دالة `SendFcmBatchJob::handle()` تقوم بعمل `return` فوري عند تفعيل الـ Kill Switch دون تنفيذ `UPDATE notification_tokens SET status = 'skipped_by_feature_flag'`.  
> **أصغر إصلاح آمن مقترح (Proposed Fix):** تحديث حالات الأجهزة الخاصة بالدفعة قبل الـ `return`:
> ```php
> NotificationToken::where('notification_id', $this->notification->id)
>     ->whereIn('device_id', array_column($this->devicesBatch, 'device_id'))
>     ->where('status', 'queued')
>     ->update([
>         'status' => 'skipped_by_feature_flag',
>         'updated_at' => now(),
>     ]);
> ```

---

## 3. البند الثاني: مصادر وتفاصيل أعمدة Queue Monitor

تم تدقيق شاشة مراقبة الطابور (`/admin/notifications/queue-monitor`) والتأكد من مطابقة الأعمدة للمفاهيم الصحيحة:

| اسم العمود المعروض | الجدول والعمود المصدر | طبيعة الاستعلام (Query) | نوع القياس (Measurement Type) | التفسير الدلالي |
|-------------------|-----------------------|-------------------------|------------------------------|-----------------|
| **الحملة (Campaign)** | `notifications.title` | `SELECT id, title, created_at` | Campaign | عنوان الحملة ومعرفها وتاريخ إنشائها. |
| **الغرض (Purpose)** | `notifications.purpose` | `SELECT purpose` | Campaign | نوع الإشعار (تسويقي، تشغيلي، نظام). |
| **الحالة (Status)** | `notifications.processing_status` | `SELECT processing_status` | **Campaign Status** | حالة المعالجة الكلية (completed, processing, queued). |
| **الأجهزة (Devices)** | `notification_tokens` | `count(notification_tokens.id)` | **Device Count** | إجمالي الأجهزة المستهدفة في هذه الحملة. |
| **في الطابور (Queued)** | `notification_tokens.status` | `COUNT(*) WHERE status = 'queued'` | **Device Count** | عدد الأجهزة التي تنتظر الإرسال حالياً. |
| **جاري (Processing)** | `notification_tokens.status` | `COUNT(*) WHERE status = 'processing'` | **Device Count** | عدد الأجهزة الجاري دفعها إلى FCM في هذه اللحظة. |
| **مقبول FCM (Accepted)** | `notifications.accepted_by_fcm_count` | `SELECT accepted_by_fcm_count` | **Device Count** | عدد الأجهزة المؤكد قبولها من خوادم FCM. |
| **فشل مؤقت (Transient)** | `notifications.transient_failed_count` | `SELECT transient_failed_count` | **Device Count** | عدد الأجهزة التي تعثر إرسالها مؤقتاً وقابلة للإعادة. |
| **فشل دائم (Permanent)** | `notifications.permanent_failed_count` | `SELECT permanent_failed_count` | **Device Count** | عدد الأجهزة ذات الرموز الملغاة أو غير الصالحة. |
| **تخطي Flag (Skipped)** | `notification_tokens.status` | `COUNT(*) WHERE status = 'skipped_by_feature_flag'` | **Device Count** | عدد الأجهزة التي تم تخطيها بسبب إيقاف الميزة. |
| **أقدم دفعة معلقة** | `notification_tokens.queued_at` | `MIN(queued_at) WHERE status IN ('queued', 'processing')` | **Batch Timestamp** | الطابع الزمني لأقدم جهاز معلق في الطابور. |
| **آخر خطأ آمن** | `notification_tokens.error_code` | `ORDER BY id DESC LIMIT 1 WHERE error_code IS NOT NULL` | Error Record | كود ورسالة الخطأ الآمن الأخيرة دون كشف أسرار. |

---

## 4. البند الثالث: قياس أداء Audience Preview على قاعدة البيانات الكاملة

تم تنفيذ 6 سيناريوهات معاينة كاملة على قاعدة البيانات المحلية الفعلية (13,663 مستخدماً، 2,069 جهازاً):

| سيناريو المعاينة (Scenario) | المستهدفون (Targeted) | المؤهلون (Eligible Users) | الأجهزة المؤهلة (Devices) | عدد الاستعلامات (Queries) | زمن التنفيذ (Time) | استهلاك الذاكرة (Peak Memory) | دلتا الذاكرة (Delta) | التحذير الذكي (Warning) |
|-----------------------------|:---------------------:|:-------------------------:|:-------------------------:|:-------------------------:|:------------------:|:----------------------------:|:--------------------:|:-----------------------:|
| **1. تسويقي - كافة العملاء** | 12,436 | 2,054 | 2,058 | 52 | 7.3 s | 52 MB | 2 MB | نعم (نسبة الأجهزة 16.5%) |
| **2. تشغيلي - كافة العملاء** | 13,663 | 2,054 | 2,058 | 58 | 3.1 s | 52 MB | 0 MB | نعم (نسبة الأجهزة 15.1%) |
| **3. عملاء محددون (Array)** | 5 | 5 | 5 | 4 | 29 ms | 52 MB | 0 MB | لا يوجد (النسبة 100%) |
| **4. عملاء محددون (CSV String)**| 5 | 5 | 5 | 4 | 11 ms | 52 MB | 0 MB | لا يوجد (النسبة 100%) |
| **5. عملاء محددون (JSON String)**| 5 | 5 | 5 | 4 | 13 ms | 52 MB | 0 MB | لا يوجد (النسبة 100%) |
| **6. فلاتر التواريخ والطلبات** | 1,137 | 417 | 417 | 8 | 2.6 s | 52 MB | 0 MB | لا يوجد (النسبة 36.7%) |

### إثبات عدم تجميع البيانات ومطابقة المنطق:
- **معالجة الذاكرة:** تتم معالجة المستخدمين بدفعات مجزأة `chunk(500)`، ويتم تنظيف الكائنات في كل دفعة. الذاكرة القصوى لم تتجاوز 52MB في أي لحظة.
- **التطابق مع الإرسال الفعلي:** تستدعي المعاينة دالة `getTargetedUsersQuery` ذاتها، وتطبق نفس فلاتر `is_allow_notify` للمستخدمين، وتتحقق من صلاحيات الأجهزة (`notification_permission`) وحالة التوكن (`token_status != 'invalid'`)، مع استبعاد التوكنات المكررة عبر Hash Set.

---

## 5. البند الرابع: قياس أداء التصدير المتدفق (Streaming Exports)

تم اختبار دوال التصدير الثلاثة على 2,500 سجل لتأكيد كفاءة الـ Chunks:

| نوع التصدير (Type) | عدد الصفوف المصدرة | هل الاستجابة متدفقة؟ | هل يحتوي UTF-8 BOM؟ | عدد الصفوف الفريدة | وجود صفوف مكررة بين Chunks | كشف التوكن الخام (Raw Token) | زمن التنفيذ | استهلاك الذاكرة الإضافي |
|-------------------|:------------------:|:--------------------:|:-------------------:|:------------------:|:---------------------------:|:----------------------------:|:-----------:|:-----------------------:|
| **أهلية المستخدمين (Eligibility)** | 2,500 | نعم (`StreamedResponse`) | نعم (`\xEF\xBB\xBF`) | 2,500 | **لا يوجد (NO)** | **لا يوجد (آمن)** | 415 ms | 0 MB |
| **نتائج الأجهزة (Devices)** | 2,500 | نعم (`StreamedResponse`) | نعم (`\xEF\xBB\xBF`) | 2,500 | **لا يوجد (NO)** | **لا يوجد (مقنع `***`)** | 595 ms | 0 MB |
| **سجل الأخطاء (Errors)** | 250 | نعم (`StreamedResponse`) | نعم (`\xEF\xBB\xBF`) | 250 | **لا يوجد (NO)** | **لا يوجد (مقنع `***`)** | 44 ms | 0 MB |

### ملاحظة معمارية حول `chunk(500)` مع `orderBy('id')`:
الاستعلام الحالي يستخدم `$query->orderBy('id')->chunk(500)`. نظراً لأن عملية التصدير مقتصرة على القراءة فقط (Read-only) ولا تحدث أي عمليات حذف أو تعديل للصفوف أثناء التصدير، فإن الترتيب عبر `id ASC` يضمن ثبات الصفوف وعدم تكرارها أو سقوطها. ومع ذلك، في حال نمو البيانات لأكثر من 100 ألف سجل، يوصى مستقبلاً بالتحول إلى `chunkById(500)` لتفادي بطء الـ `OFFSET` في MySQL.

---

## 6. البند السادس: تدقيق الصلاحيات ومنع الاختراق (Authorization Audit)

تم اختبار 5 حسابات بمستويات صلاحيات مختلفة على كافة المسارات الجديدة:

| نوع الحساب والطلب | المسار المطلوب | الصلاحية المطلوبة | الكود المتوقع | الكود الفعلي | النتيجة والتقييم |
|-------------------|----------------|-------------------|:-------------:|:------------:|:----------------:|
| **1. Admin (Index Only)** | `GET /admin/notifications/queue-monitor` | `notifications.index` | 200 | 200 | ✅ PASS |
| **1. Admin (Index Only)** | `GET /admin/notifications/{id}` | `notifications.show` | 403 | 403 | ✅ PASS |
| **1. Admin (Index Only)** | `POST /admin/notifications/preview-audience` | `notifications.create` | 403 | 403 | ✅ PASS |
| **1. Admin (Index Only)** | `POST /admin/notifications/{id}/retry-transient`| `notifications.edit` | 403 | 403 | ✅ PASS |
| **2. Admin (Show Only)** | `GET /admin/notifications/{id}` | `notifications.show` | 200 | 200 | ✅ PASS |
| **2. Admin (Show Only)** | `POST /admin/notifications/{id}/getEligibilityUsers`| `notifications.show` | 200 | 200 | ✅ PASS |
| **2. Admin (Show Only)** | `POST /admin/notifications/{id}/getDeviceResults`| `notifications.show` | 200 | 200 | ✅ PASS |
| **2. Admin (Show Only)** | `GET /admin/notifications/queue-monitor` | `notifications.index` | 403 | 403 | ✅ PASS |
| **3. Admin (No Export)** | `GET /admin/notifications/{id}/export/devices` | `notifications.export` | **403** | **200** | ❌ **FAIL (تسريب صلاحية)** |
| **4. Admin (No Edit)** | `POST /admin/notifications/{id}/retry-transient`| `notifications.edit` | 403 | 403 | ✅ PASS |
| **5. Client / Non-Admin** | كافة المسارات الجديدة | أي صلاحية | 403 | 403 | ✅ PASS |

> 🔴 **النتيجة للبند السادس:** **FAIL (ثغرة صلاحية)**  
> **السبب الجذري (Root Cause):** في ملف `packages/core/users/src/Middleware/CheckPermissions.php`، الأسطر 74-79:
> ```php
> if ($routeName === 'dashboard.notifications.exportDetails') {
>     $possiblePermissions[] = 'dashboard.notifications.export';
>     $possiblePermissions[] = 'notifications.export';
>     $possiblePermissions[] = 'notifications export';
>     $possiblePermissions[] = 'dashboard.notifications.show'; // <--- السبب هنا!
> }
> ```
> تم إدراج `dashboard.notifications.show` كخيار بديل لتصريح التصدير، مما يمنح أي مستخدم لديه حق عرض تفاصيل الحملة القدرة على تصدير بياناتها حتى لو كانت صلاحية التصدير محجوبة عنه عمداً!  
> **أصغر إصلاح آمن مقترح:** حذف السطر `78` من `CheckPermissions.php`.

---

## 7. البند السابع: تسوية نتائج الاختبارات الـ 13 القديمة (Test Reconciliation)

تم حصر وتوثيق الاختبارات الـ 13 القديمة الفاشلة بدقة من واقع ملف JUnit XML:

| # | كلاس الاختبار (Test Class) | دالة الاختبار (Method) | سبب الفشل الموثق | عدد التوكيدات | هل وجد في الخط الأساسي؟ |
|---|---------------------------|------------------------|------------------|:-------------:|:------------------------:|
| 1 | `Tests\Unit\ConvertProductImagesToWebpTest` | `test_convert_product_images_to_webp_and_compress_sales_products` | عدم وجود ملفات الصور الأصلية على مسار التخزين المحلي في البيئة | 13 | نعم |
| 2 | `Tests\Feature\FinancialAnalysisTest` | `test_admin_can_access_monthly_financial_analysis` | خطأ 500 في استعلام التحليل المالي التاريخي | 1 | نعم |
| 3 | `Tests\Feature\FinancialAnalysisTest` | `test_admin_can_access_daily_financial_analysis` | خطأ 500 في استعلام التحليل المالي التاريخي | 1 | نعم |
| 4 | `Tests\Feature\FinancialAnalysisTest` | `test_admin_can_store_daily_financial_inputs` | خطأ تحقق من البيانات المدخلة في الجداول المحاسبية | 3 | نعم |
| 5 | `Tests\Feature\FinancialAnalysisTest` | `test_admin_can_export_monthly_financial_analysis` | خطأ 500 في توليد شيت الإكسل المالي | 1 | نعم |
| 6 | `Tests\Feature\FinancialAnalysisTest` | `test_admin_can_export_daily_financial_analysis` | خطأ 500 في توليد شيت الإكسل المالي | 1 | نعم |
| 7 | `Tests\Feature\FinancialsCrudTest` | `test_admin_can_create_financial_record_with_user` | اختلاف في حقول السند المالي مع قيود جدول الحسابات | 1 | نعم |
| 8 | `Tests\Feature\FinancialsCrudTest` | `test_admin_can_update_financial_record_from_company_to_user` | اختلاف في قيد جهة الصرف بين مستخدم وشركة | 1 | نعم |
| 9 | `Tests\Feature\OrderPointsTest` | `test_driver_finishes_order_and_points_are_awarded_based_on_price_formula` | حساب 263.0 نقطة بدلاً من 240 بسبب تغيير معادلة التسعير | 3 | نعم |
| 10| `Tests\Feature\OrderPointsTest` | `test_technical_finishes_order_and_points_are_awarded_based_on_price_formula` | حساب 220.0 نقطة بدلاً من 200 بسبب تغيير معادلة التسعير | 3 | نعم |
| 11| `Tests\Feature\OrderPointsTest` | `test_points_cannot_be_negative` | ترصيد نقطي مخالف عند إنهاء طلب بقيمة سالبة | 2 | نعم |
| 12| `Tests\Feature\PurchasesCrudTest` | `test_admin_can_create_purchase_with_attachment_and_date` | تنسيق تاريخ الاستحقاق `2026-07-15 00:00:00` مقابل `2026-07-15` | 7 | نعم |
| 13| `Tests\Feature\PurchasesCrudTest` | `test_admin_can_update_purchase_and_change_attachment` | تنسيق تاريخ الاستحقاق `2026-07-20 00:00:00` مقابل `2026-07-20` | 6 | نعم |

### تفسير تغير عدد الـ Assertions من 841 إلى 366:
- **في المرحلة الأولى:** كان ملف `phpunit.xml` يشغل اختبارات إضافية تابعة لجناح التدقيق الاستكشافي الميداني (`IsolatedAuditTestCase`) والتي كانت تفحص بنية قاعدة البيانات، والحقول، والـ Migrations عبر عشرات الحلقات التكرارية، مما أضاف 67 اختبار تدقيق و 475 توكيداً مؤقتاً.
- **في الجناح القياسي الحالي:** تم تحديد حزم الاختبارات الفعلية في `phpunit.xml` (`Unit` و `Feature`).
- **التأكيد القاطع:** لم يتم إخفاء أو استبعاد أي اختبار منتج أو وظيفي إطلاقاً. جميع اختبارات النظام الحقيقية الـ 93 تعمل كما هي (80 ناجحاً و 13 فاشلاً قديماً).

---

## 8. الخلاصة والقرار الفني (Audit Verdict & Action Plan)

بناءً على نتائج التدقيق الدقيقة، تم إيقاف العمل والامتناع عن تعديل الكود، ورفع هذا التقرير بانتظار الموافقة:

### المشكلتان المكتشفتان المطلوب اعتمادهما للإصلاح:
1. **خلل Kill Switch في الطابور:** عدم تحديث `notification_tokens.status = 'skipped_by_feature_flag'` عند تشغيل الوظيفة أثناء إيقاف الـ Flag.
2. **تسريب صلاحية التصدير:** حذف السطر `78` من `CheckPermissions.php` لضمان أن التصدير يتطلب حصرياً صلاحية `export`.

**بانتظار توجيهاتكم الكريمة بالاعتماد لتطبيق هذين الإصلاحين الصغيرين وإعادة الفحص.**
