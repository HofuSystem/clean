# SAFE PRODUCTION BACKEND DEPLOYMENT PLAN — DIRECT FCM DISABLED
## دليل التشغيل وخطة التنفيذ خطوة بخطوة للإنتاج (Production Deployment Runbook)

**تاريخ الإصدار:** 2026-09-28  
**المشروع:** CleanStation Backend  
**الهدف:** النشر الآمن الأول للـ Backend، لوحة تحكم الإشعارات، الـ APIs الجديدة، والـ Migrations مع إبقاء المسار الفعلي للإشعارات هو Legacy Topic Path، وحظر Direct FCM نهائياً.

---

## 1. المبادئ الحاكمة والمحظورات الصارمة (Strict Deployment Invariants)

1. **`NOTIFICATION_DIRECT_FCM_ENABLED=false` حتماً ولا تفاوض فيه:**
   - يبقى نظام الإشعارات الفعلي يعمل عبر المسار القديم (Legacy Topic Broadcast).
   - لا يتم إرسال أي رسائل FCM فردية Direct إلى عملاء حقيقيين.
2. **عدم حجب أي مستخدم أو جهاز (Observe Mode Only):**
   - `NOTIFICATION_PERMISSION_MODE=observe`
   - `NOTIFICATION_MARKETING_PREFERENCE_MODE=observe`
   - لا يتم حظر أي إشعار بناءً على إذن النظام أو التفضيلات التسويقية في هذه المرحلة.
3. **التوافق العكسي التام (Zero Client Breaking Changes):**
   - لا تعديل على كود Flutter.
   - لا اشتراط لتحديث التطبيقات لدى العملاء أو السائقين أو الفنيين.
   - استمرار عمل الـ Endpoints القديمة (`/api/devices/update_fcm` وغيرها) بجانب الـ APIs الجديدة.
4. **حماية وسلامة البيانات التاريخية (Data Preservation):**
   - ممنوع تشغيل `migrate:fresh` أو `migrate:rollback`.
   - ممنوع حذف أو تعديل سجلات قديمة أو تنظيف أجهزة قديمة.
   - ممنوع حذف `failed_jobs` أو إعادة تشغيلها جماعياً.
   - الحفاظ على الـ 1,050 سجلاً التجريبي الموجودة في قاعدة Local وعدم نقلها أو تكرارها.

---

## 2. جدول المتغيرات البيئية الإلزامية للإنتاج (Production .env Configuration)

يجب ضبط القيم التالية في ملف `.env` الخاص بالإنتاج قبل تنفيذ أي خطوة:

```dotenv
# ==============================================================================
# NOTIFICATION SYSTEM PRE-PRODUCTION SAFETY FLAGS (PRODUCTION VALUES)
# ==============================================================================
NOTIFICATION_DEVICE_SYNC_ENABLED=true
NOTIFICATION_DIRECT_FCM_ENABLED=false
NOTIFICATION_PERMISSION_MODE=observe
NOTIFICATION_MARKETING_PREFERENCE_MODE=observe
NOTIFICATION_DELIVERY_EVENTS_ENABLED=false
NOTIFICATION_NEW_DASHBOARD_METRICS_ENABLED=true
```

---

## 3. خطة التنفيذ الميدانية مرحلة بمرحلة (Step-by-Step Execution Runbook)

```mermaid
flowchart TD
    A[المرحلة 1: Read-Only Preflight] --> B[المرحلة 2: Database Backup]
    B --> C[المرحلة 3: Environment Safety Flags]
    C --> D[المرحلة 4: Code Deployment Precheck]
    D --> E[المرحلة 5: Migration Dry-Run --pretend]
    E --> F[المرحلة 6: Controlled Migration & Cache]
    F --> G[المرحلة 7: Queue Worker Restart]
    G --> H[المرحلة 8: Post-Deploy Verification]
    H --> I[المرحلة 9: 60-Minute Monitoring Window]
```

### المرحلة 1: Production Read-Only Preflight (الفحص الاستطلاعي)
يتم تنفيذ الأوامر التالية على سيرفر الإنتاج لتوثيق الحالة قبل أي تغيير:
```bash
# 1. التحقق من المسار والمضيف وبيئة التشغيل
pwd
hostname
whoami

# 2. التحقق من إصدارات النظام
php -v
php artisan --version

# 3. التحقق من حالة Git
git status --short
git branch -v
git rev-parse HEAD

# 4. التحقق من إعدادات الطابور وقاعدة البيانات
php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'APP_ENV=' . config('app.env') . PHP_EOL;
echo 'DB_DATABASE=' . config('database.connections.' . config('database.default') . '.database') . PHP_EOL;
echo 'QUEUE_CONNECTION=' . config('queue.default') . PHP_EOL;
echo 'QUEUE_RETRY_AFTER=' . config('queue.connections.' . config('queue.default') . '.retry_after') . PHP_EOL;
"

# 5. عد الوظائف في الطوابير والمساحة المتاحة
php artisan queue:monitor default
df -h
```
> [!CAUTION]
> **شرط التوقف الصارم:** إذا كان `APP_ENV != production` أو ظهرت تعديلات محلية غير معروفة في مجلد Git على السيرفر، **توقف فوراً ولا تتابع النشر**.

---

### المرحلة 2: النسخة الاحتياطية الكاملة (Database Backup & Snapshots)
قبل تنفيذ أي Migration، يتم أخذ نسخة احتياطية مشفرة ومؤرخة:

```bash
# 1. إنشاء مجلد النسخ الاحتياطي
BACKUP_DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="/backups/cleanstation_prod_${BACKUP_DATE}.sql.gz"

# 2. أخذ النسخة الكاملة عبر mysqldump
mysqldump -u <DB_USER> -p<DB_PASSWORD> --single-transaction --quick --routines --triggers CleanStation | gzip > "${BACKUP_FILE}"

# 3. توثيق الـ Checksum والحجم
ls -lh "${BACKUP_FILE}"
sha256sum "${BACKUP_FILE}" > "${BACKUP_FILE}.sha256"

# 4. أخذ Snapshot لأعداد الجداول الأساسية
mysql -u <DB_USER> -p<DB_PASSWORD> CleanStation -e "
SELECT 'users' as tbl, count(*) as count FROM users
UNION ALL SELECT 'devices', count(*) FROM devices
UNION ALL SELECT 'notifications', count(*) FROM notifications
UNION ALL SELECT 'users_notifications', count(*) FROM users_notifications
UNION ALL SELECT 'notification_tokens', count(*) FROM notification_tokens
UNION ALL SELECT 'jobs', count(*) FROM jobs
UNION ALL SELECT 'failed_jobs', count(*) FROM failed_jobs;
"
```

---

### المرحلة 3: تأكيد وتطبيق حواجز الأمان (Environment Safety Flags)
1. تعديل ملف `.env` على السيرفر لإضافة المتغيرات الستة الإلزامية.
2. التحقق من قراءة القيم عبر عملية PHP مستقلة تماماً:
```bash
php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
assert(config('notification.direct_fcm_enabled') === false, 'CRITICAL: Direct FCM is enabled!');
assert(config('notification.permission_mode') === 'observe', 'CRITICAL: Permission mode not observe!');
assert(config('notification.marketing_preference_mode') === 'observe', 'CRITICAL: Marketing mode not observe!');
echo 'SAFETY FLAGS VERIFIED: ALL SAFE' . PHP_EOL;
"
```

---

### المرحلة 4: فحص الكود قبل النشر (Code Deployment Precheck)
```bash
# 1. جلب التحديثات
git fetch origin

# 2. مراجعة التغييرات القادمة مقارنة بـ HEAD
git log --oneline HEAD..origin/main
git diff --stat HEAD..origin/main

# 3. سحب التحديثات بطريقة آمنة
git pull --ff-only origin main
```

---

### المرحلة 5: محاكاة الـ Migration (Migration Dry Run)
تنفيذ المحاكاة وقراءة استعلامات SQL الناتجة قبل تطبيقها فعلياً:
```bash
php artisan migrate --pretend
```

#### التحقق الإلزامي من الاستعلامات:
- [x] جميع الاستعلامات عبارة عن `ALTER TABLE ... ADD COLUMN` فقط.
- [x] جميع الأعمدة الجديدة Nullable أو تمتلك قيم Defaults افتراضية آمنة.
- [x] الفهارس المضافة عادية وغير فريدة (`INDEX` وليس `UNIQUE`).
- [x] لا توجد أوامر `DROP` أو `TRUNCATE` أو `RENAME`.
- [x] عدم وجود `UNIQUE(device_token)` في جدول `devices`.

---

### المرحلة 6: التنفيذ المحكوم للنشر (Controlled Deployment Execution)

```bash
# 1. إدخال التطبيق في وضع الصيانة القصير (اختياري بحسب نافذة الصيانة)
php artisan down --secret="cleanstation-deploy-2026" --render="errors::503"

# 2. تحديث التبعيات في حال تغير composer.lock
composer install --no-dev --optimize-autoloader --no-interaction

# 3. تنفيذ الـ Migrations الفعلية
php artisan migrate --force

# 4. تنظيف وإعادة بناء الكاش المتوافق
php artisan optimize:clear
php artisan config:cache

# 5. إعادة تشغيل الطوابير
php artisan queue:restart

# 6. إذا كان Supervisor يدير العمال:
sudo supervisorctl restart all

# 7. إخراج التطبيق من وضع الصيانة
php artisan up
```

---

### المرحلة 7: الفحص الفوري بعد النشر (Post-Deploy Smoke Verification)

يتم التحقق الفوري من الخدمات الحيوية عبر حسابات اختبار تجريبية محددة حصراً (ممنوع الإرسال العام):

1. **الصفحة الرئيسية ولوحة التحكم:**
   - فحص لوحة الإشعارات الجديدة: `GET /dashboard/notifications`
   - فحص شاشة المراقبة الصحية: `GET /dashboard/notifications/health`
   - فحص مراقبة الطوابير: `GET /dashboard/notifications/queue-monitor`
2. **تطبيقات العملاء والفنيين والسائقين (Legacy Compatibility):**
   - تسجيل الدخول لعميل قديم.
   - استدعاء تحديث توكن FCM القديم (`POST /api/devices/update_fcm`).
   - استدعاء المزامنة الجديدة (`POST /api/devices/sync`) والتأكد من استجابة `200 OK`.
   - إنشاء طلب تجريبي واختبار إشعار Transactional قديم (SMS/WhatsApp/Legacy Push).
   - التأكد من عدم تأثر `BannerNotification`.

---

### المرحلة 8: التحقق من سلامة البيانات وقاعدة البيانات (Database Integrity Verification)

مقارنة الأعداد مع الـ Snapshot المأخوذ في المرحلة 2:
```bash
mysql -u <DB_USER> -p<DB_PASSWORD> CleanStation -e "
SELECT 'users' as tbl, count(*) as count FROM users
UNION ALL SELECT 'devices', count(*) FROM devices
UNION ALL SELECT 'notifications', count(*) FROM notifications
UNION ALL SELECT 'users_notifications', count(*) FROM users_notifications
UNION ALL SELECT 'notification_tokens', count(*) FROM notification_tokens;
"
```
**معايير القبول:**
- الأعداد لم تنخفض إطلاقاً.
- `notification_permission` لجميع الأجهزة التاريخية = `unknown`.
- `token_status` للأجهزة التاريخية = `valid`.
- `is_allow_notify_confirmed_at` للمستخدمين القدامى = `NULL`.
- `notification_tokens` للحملات القديمة غير ممسوسة.

---

### المرحلة 9: نافذة المراقبة (60-Minute Monitoring Window)

مراقبة مستمرة لمدة 60 دقيقة عبر المراحل الزمنية:
- **T+0 (فوراً):** فحص `storage/logs/laravel.log`، والتأكد من غياب أي Exceptions.
- **T+15 دقيقة:** فحص طابور الوظائف `jobs` و `failed_jobs`. التأكد من معالجة الوظائف بشكل طبيعي وعدم تراكمها.
- **T+30 دقيقة:** فحص قناة تيليجرام للأخطاء (Telegram Exception Channel) وتأكيد عدم وجود أخطاء N+1 أو أخطاء استعلامات.
- **T+60 دقيقة:** تأكيد استقرار معدلات الاستجابة واستهلاك الذاكرة للعمال.

---

## 4. خطة التراجع السريع عند الطوارئ (Rollback Emergency Runbook)

إذا ظهر أي خلل غير متوقع يهدد استقرار التطبيق:

1. **الاحتفاظ بالـ Migrations كما هي:** نظراً لأن جميع الأعمدة الجديدة Additive و Nullable، فإنها لا تعطل الكود القديم ولا تتطلب أي Rollback لقاعدة البيانات.
2. **الرجوع لـ Commit السابق:**
   ```bash
   git checkout <PREVIOUS_COMMIT_SHA>
   ```
3. **تأكيد بقاء Direct FCM معطلاً:**
   ```dotenv
   NOTIFICATION_DIRECT_FCM_ENABLED=false
   ```
4. **تحديث الكاش وإعادة تشغيل الطابور:**
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan queue:restart
   sudo supervisorctl restart all
   ```
5. **التحقق من عمل المسارات القديمة بالكامل.**

---

## 5. بوابات تفعيل Direct FCM المؤجلة (Deferred Direct FCM Gates)

**يُمنع منعاً باتاً تفعيل `NOTIFICATION_DIRECT_FCM_ENABLED=true` على الإنتاج في هذه المرحلة.**
لا يجوز النظر في تفعيله إلا بعد استيفاء البوابات التالية مستقبلاً:
1. اختبار دفعة كلها فشل دائم (All-permanent batch query benchmark).
2. اختبار تحول الحالات من `transient_failed` إلى `accepted` في العدادات.
3. اختبار تحول الحالات من `transient_failed` إلى `permanent_failed`.
4. اختبار حماية التنافس لنفس التوكن بين عمال متعددين.
5. تجربة Canary مغلقة على هواتف فريق العمل التجريبية فقط.
6. تطوير وإطلاق دعم أحداث الاستلام والفتح (received/opened) في تطبيق Flutter.