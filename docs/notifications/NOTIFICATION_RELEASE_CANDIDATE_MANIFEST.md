# NOTIFICATION RELEASE CANDIDATE MANIFEST

**تاريخ الإصدار:** 2026-09-28  
**المشروع:** CleanStation Backend  
**Commit الأساس (Base Commit):** `17d9f880bc4430835eb3a02ff759afc91ace5fae`  
**الفرع:** `main`  
**حالة Direct FCM:** `direct_fcm_enabled = false` (معطل ومحظور افتراضياً)  

---

## 1. جدول النطاق الشامل لملفات الإصدار المعتمدة (Release Scope Manifest)

| # | مسار الملف (File Path) | نوع الملف (Type) | سبب دخوله في الإصدار (Rationale) | السلوك عند Direct FCM=false | الأثر على التطبيق القديم (Legacy Impact) |
|:---:|---|---|---|:---:|---|
| 1 | `config/notification.php` | Production Config | حواضر الأمان والمتغيرات البيئية الافتراضية للنظام الجديد | آمن تماماً، يعود لـ Legacy | معدوم (تكوين خادم فقط) |
| 2 | `.env.example` | Config Template | توثيق المتغيرات البيئية الجديدة للإنتاج | مجرد توثيق | معدوم |
| 3 | `app/Providers/AppServiceProvider.php` | Core Provider | تحميل migrations حزم packages في بيئة testing | غير نشط في الإنتاج | معدوم |
| 4 | `packages/core/notification/src/database/migrations/2026_09_26_180003_add_campaign_tracking_columns_to_notifications_table.php` | Migration | تتبع الحملات والإحصائيات التراكمية بحقول Nullable/Default 0 | جاهز لتسجيل الإحصائيات | معدوم (حقول إضافية غير إلزامية) |
| 5 | `packages/core/notification/src/database/migrations/2026_09_26_180004_add_eligibility_columns_to_users_notifications_table.php` | Migration | تتبع أهلية استلام المستخدمين للأجهزة بحقول Nullable | يسجل أهلية الملاحظة | معدوم |
| 6 | `packages/core/notification/src/database/migrations/2026_09_26_180005_expand_notification_tokens_table.php` | Migration | تدقيق نتائج كل جهاز وتفادي N+1 | يسجل التوكنات وتدقيقها | معدوم |
| 7 | `packages/core/users/src/database/migrations/2026_09_26_180001_add_device_management_columns_to_devices_table.php` | Migration | إضافة حقول الأجهزة وحالة التوكن (لا يوجد UNIQUE) | يقبل الأجهزة القديمة كـ valid | معدوم |
| 8 | `packages/core/users/src/database/migrations/2026_09_26_180002_add_is_allow_notify_confirmed_at_to_users_table.php` | Migration | تأكيد إذن الإشعارات دون المساس بالمستخدمين القدامى | Nullable للجميع | معدوم |
| 9 | `packages/core/notification/src/Controllers/Api/NotificationEventsController.php` | Controller | استقبال أحداث استلام وفتح الإشعارات (معطل بـ Flag) | مغلق بـ 403 Forbidden | معدوم |
| 10 | `packages/core/notification/src/Controllers/Dashboard/NotificationsController.php` | Controller | إدارة الحملات وإحصائيات التسليم وعمليات المراقبة والصلاحيات | يعرض إحصائيات Legacy و Direct | معدوم |
| 11 | `packages/core/notification/src/DataResources/NotificationsResource.php` | Resource | تحويل بيانات الحملة للمؤشرات المحدثة | متوافق بالكامل | معدوم |
| 12 | `packages/core/notification/src/Helpers/NotificationDataNormalizer.php` | Helper | تطبيع بيانات الإشعار للـ Payload الموحد | نشط وتوافقي | معدوم |
| 13 | `packages/core/notification/src/Helpers/NotificationsManger.php` | Service Helper | مسار التوجيه بين Legacy Topic و Direct FCM وفق الـ Flag | يوجه 100% إلى Legacy Path | يحفظ السلوك القديم كاملاً |
| 14 | `packages/core/notification/src/Jobs/SendFcmBatchJob.php` | Queue Job | وظيفة معالجة الدفعات المجمعة مع الـ Kill Switch الذاتي | يتخطى الدفعات فورياً إذا عُطل | معدوم |
| 15 | `packages/core/notification/src/Models/Notification.php` | Eloquent Model | العلاقات وحساب المؤشرات والمورف | متوافق بالكامل | معدوم |
| 16 | `packages/core/notification/src/Models/NotificationToken.php` | Eloquent Model | نموذج تدقيق سجل توكنات الإرسال | متوافق بالكامل | معدوم |
| 17 | `packages/core/notification/src/Models/UsersNotification.php` | Eloquent Model | عزل المورف لـ Notification عن BannerNotification | يحمي السجلات القديمة | معدوم |
| 18 | `packages/core/notification/src/Services/FCMService.php` | Core Service | المعالجة المجمعة Bulk، والزيادة الذرية لـ attempts | معطل حياً، يرجع Legacy | معدوم |
| 19 | `packages/core/notification/src/Services/NotificationsService.php` | Core Service | استعلامات الفلترة وجداول لوحة التحكم وتفادي N+1 | نشط ويوفر أداءً عالياً | معدوم |
| 20 | `packages/core/notification/src/Services/RecipientEligibilityService.php` | Service | فحص أذونات وأهلية الجمهور بنمط observe | يسجل الملاحظات دون حجب | معدوم |
| 21 | `packages/core/notification/src/resources/views/pages/notifications/edit.blade.php` | Blade View | واجهة تعديل وإنشاء الإشعارات المحدثة | متوافقة مع المتصفحات | معدوم |
| 22 | `packages/core/notification/src/resources/views/pages/notifications/health.blade.php` | Blade View | لوحة الصحة والتشخيص الفوري | لوحة تحكم إدارية فقط | معدوم |
| 23 | `packages/core/notification/src/resources/views/pages/notifications/list.blade.php` | Blade View | واجهة قائمة الحملات المحدثة بالمؤشرات | لوحة تحكم إدارية فقط | معدوم |
| 24 | `packages/core/notification/src/resources/views/pages/notifications/queue-monitor.blade.php` | Blade View | شاشة مراقبة طوابير الإشعارات وحالتها | لوحة تحكم إدارية فقط | معدوم |
| 25 | `packages/core/notification/src/resources/views/pages/notifications/show.blade.php` | Blade View | شاشة تفاصيل الحملة وتدقيق الأجهزة والتصدير | لوحة تحكم إدارية فقط | معدوم |
| 26 | `packages/core/notification/src/routes/api.php` | Route File | تسجيل مسارات أحداث استلام الإشعارات المحمية | محمي بالـ Feature Flag | معدوم |
| 27 | `packages/core/notification/src/routes/dashboard.php` | Route File | تسجيل مسارات لوحة التحكم وتصدير التقارير المحمية | محمي بالـ Permissions | معدوم |
| 28 | `packages/core/users/src/Controllers/Api/DeviceSyncController.php` | Controller | Endpoint مزامنة الأجهزة الحديث وتفضيلات التسويق | متاح للأجهزة الداعمة | معدوم |
| 29 | `packages/core/users/src/Controllers/Api/UserController.php` | Controller | دعم التوافق العكسي لـ update_fcm وتطبيع app_context | يستقبل طلبات التطبيق القديم 100% | يحافظ على التطبيق القديم |
| 30 | `packages/core/users/src/Middleware/CheckPermissions.php` | Middleware | حماية صلاحيات تصدير البيانات (RBAC Export Gate) | نشط أمنياً | معدوم |
| 31 | `packages/core/users/src/Models/Device.php` | Eloquent Model | نموذج الأجهزة بالحقول الجديدة والـ Scope | متوافق بالكامل | معدوم |
| 32 | `packages/core/users/src/Models/User.php` | Eloquent Model | إضافة is_allow_notify_confirmed_at للـ fillable | متوافق بالكامل | معدوم |
| 33 | `packages/core/users/src/Services/DevicesService.php` | Service | خدمة إدارة وتحديث الأجهزة وحماية سباق التوكن | متوافق بالكامل | معدوم |
| 34 | `packages/core/users/src/resources/views/pages/users/show.blade.php` | Blade View | عرض بيانات وتوكنات أجهزة العميل بلوحة التحكم | لوحة تحكم إدارية فقط | معدوم |
| 35 | `packages/core/users/src/routes/api.php` | Route File | تسجيل مسارات devices/sync و client/notification-preference | مسارات إضافية جديدة | معدوم |
| 36 | `phpunit.xml` | Test Config | تكوين عزل الاختبارات وذاكرة 512M | بيئة الاختبار فقط | معدوم |
| 37 | `tests/Support/IsolatedAuditTestCase.php` | Test Support | فئة الاختبارات المعزولة في الذاكرة | بيئة الاختبار فقط | معدوم |
| 38 | `tests/TestCase.php` | Test Base | حارس منع مساس الاختبارات بقاعدة الإنتاج أو المحلية | بيئة الاختبار فقط | معدوم |
| 39 | `tests/Feature/LegacyCompatibilityGateTest.php` | Test | التحقق من التوافق العكسي للأجهزة والتطبيقات القديمة | اختبار مؤكد 100% | معدوم |
| 40 | `tests/Feature/NotificationAdminDashboardPhase2Test.php` | Test | التحقق من صلاحيات وشاشات لوحة تحكم الإشعارات | اختبار مؤكد 100% | معدوم |
| 41 | `tests/Feature/NotificationPerformanceRemediationTest.php` | Test | اختبارات الـ Concurrency والـ 16 سيناريو للأداء ومعالجة N+1 | اختبار مؤكد 100% | معدوم |
| 42 | `tests/Feature/NotificationRemediationPhase1Test.php` | Test | اختبارات حواجز الأمان وتوافق التطبيقات القديمة | اختبار مؤكد 100% | معدوم |
| 43 | `tests/Feature/NotificationRemediationPhase21Test.php` | Test | اختبارات حماية التصدير وأذونات التسويق | اختبار مؤكد 100% | معدوم |
| 44 | `tests/Feature/NotificationRemediationPhase22Test.php` | Test | اختبارات معالجة البيانات وتطبيع الأدوار | اختبار مؤكد 100% | معدوم |
| 45 | `tests/Feature/RealDatabaseQueueWorkerAuditTest.php` | Test | اختبارات تشغيل عمال الطابور الحقيقيين والـ Kill Switch | اختبار مؤكد 100% | معدوم |
| 46 | `tests/Feature/StagingControlledFCMVerificationTest.php` | Test | اختبارات التحقق من قنوات الإرسال المراقبة | اختبار مؤكد 100% | معدوم |
| 47 | `tests/Feature/StagingDashboardVerificationTest.php` | Test | اختبارات مؤشرات لوحة التحكم | اختبار مؤكد 100% | معدوم |
| 48 | `tests/Feature/StagingKillSwitchAndFailClosedTest.php` | Test | اختبارات صمام الأمان وإغلاق النظام الآمن | اختبار مؤكد 100% | معدوم |
| 49 | `docs/notifications/SAFE_PRODUCTION_BACKEND_DEPLOYMENT_PLAN.md` | Doc | دليل التشغيل التفصيلي الميداني للإنتاج | وثيقة تشغيل | معدوم |
| 50 | `docs/notifications/SAFE_PRODUCTION_BACKEND_DEPLOYMENT_REPORT.md` | Doc | تقرير جاهزية النشر والفحص الاستطلاعي | وثيقة تدقيق | معدوم |
| 51 | `docs/notifications/LOCAL_PERFORMANCE_REMEDIATION_REPORT.md` | Doc | تقرير معالجة مشكلة N+1 وقياسات الأداء | وثيقة تدقيق | معدوم |
| 52 | `docs/notifications/LOCAL_BENCHMARK_DATA_CLEANUP_DRY_RUN.md` | Doc | تقرير حصر السجلات التجريبية دون حذفها | وثيقة تدقيق | معدوم |

---

## 2. جدول الملفات المستبعدة قطعياً من الـ Commit (Excluded Files)

| الملف المستبعد | التصنيف | سبب الاستبعاد الصارم |
|---|---|---|
| `packages/core/users/src/Controllers/Api/BranchWebhookController.php` | F. Unrelated | تطوير خارجي لـ Branch attribution غير مرتبط بالإشعارات |
| `packages/core/users/src/Controllers/Api/UserAttributionController.php` | F. Unrelated | تطوير خارجي لـ User attribution غير مرتبط بالإشعارات |
| `packages/core/users/src/Models/BranchEventLog.php` | F. Unrelated | نموذج خاص بـ Branch attribution غير مرتبط بالإشعارات |
| `packages/core/users/src/Models/UserAttribution.php` | F. Unrelated | نموذج خاص بـ User attribution غير مرتبط بالإشعارات |
| `packages/core/users/src/database/migrations/2026_09_17_150000_create_user_attributions_table.php` | F. Unrelated | migration سابقة خاصة بـ Branch attribution |
| `packages/core/users/src/database/migrations/2026_09_17_150001_create_branch_event_logs_table.php` | F. Unrelated | migration سابقة خاصة بـ Branch attribution |
| `tests/Unit/MediaCenterHelperTest.php` | F. Unrelated | تعديل مؤقت لفئة اختبار خارجية لا تخص الإشعارات |
| `.env` | G. Sensitive | ملف بيئة محلي يحتوي أسرار وبيانات تشغيل |
| `database/testing.sqlite` وجميع `*.sqlite` | E. Runtime | قواعد بيانات محلية للاختبار فقط |
| `scratch/` | E. Runtime | مجلد السكريبتات المؤقتة والتقارير الوسيطة |
| `storage/` | E. Runtime | مجلد الكاش والملفات المؤقتة |