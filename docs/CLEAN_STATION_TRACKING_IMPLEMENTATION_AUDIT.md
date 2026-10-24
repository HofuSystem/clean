# تدقيق تنفيذ التتبع — Clean Station

**تاريخ الفحص:** 2026-10-19  
**الطريقة:** قراءة ساكنة للملفات المتتبعة في المستودع. لم تُشغّل الاختبارات أو أوامر Laravel، ولم يُتصل بقاعدة بيانات أو خدمة خارجية.

## 1. الحكم وحدود الفحص

التنفيذ الحالي يحتوي أساسًا صالحًا لبعض تتبع الباك إند: سياق إحالة اختياري للطلب، جدول Outbox بسجل مستقل لكل وجهة ومفتاح فريد، مرسل مجدول إلى GA4 وBranch، وبناء جزئي لحمولة الشراء من بيانات الطلب. لكنه **لا يطابق الخطة الواردة في الطلب**: `main_section=laundry_station` غائب، أحداث دورة الاستلام والتسليم النهائية غير مكتملة أو بأسماء/توقيت مختلف، `purchase` ليس مربوطًا بـ`finished`، وتتبع المحفظة الترويجي والنقاط والكوبونات غير موجود في طبقة التحليلات المفحوصة. لا يمكن الحكم على أحداث التطبيق لأن مصدر Flutter غير موجود.

المقارنة مع وثيقة `CLEAN STATION — Tracking Plan تنفيذي (غسيل STATION).docx` لم تتم؛ لم يُعثر عليها ضمن الملفات المتاحة. المطابقة أدناه مبنية على نص الخطة والأحداث الـ31 المرفقة مع الطلب. كما لا يمكن إثبات أن نسخة `main` هي نسخة الإنتاج المنشورة.

المستودع Laravel/PHP مع موقع ثابت `cleanstation.app`. ملفات Flutter/Dart غير موجودة ضمن الملفات المفحوصة. فُحصت أسماء متغيرات البيئة المستخدمة في `config/analytics.php` فقط؛ لم تُقرأ قيم `.env` ولم تُتحقق من كونها غير فارغة أو من القيم المحملة في الإنتاج.

## 2. نسخة المصدر

- الفرع: `main`
- Commit: `a64a45c6c58e72ca2afe8546dcd08561638aa7e4`
- حالة Git عند بدء الفحص: لا تغييرات متتبعة؛ يوجد ملف/مجلد غير متتبعان `CleanStation.rar` و`docs/analytics/` ولم يُعدّلا.
- لا توجد `AGENTS.md` في شجرة المشروع.
- مستندات DOCX الموجودة لا تتضمن وثيقة خطة التتبع المذكورة. ملفات التطبيق، إعداد Firebase/GA4/Branch الفعلية على الإنتاج، لوحة المنصات، BigQuery، وسجلات قاعدة البيانات الإنتاجية ليست ضمن نطاق إثبات هذا الفحص.

## 3. ملخص مسار الإرسال الموجود

1. `CreateOrderRequest` يقبل `analytics_context` اختياريًا ويقيّد بنيته وحقوله؛ التوافق الاختياري يعني أن طلبات العملاء القديمة والطلبات التي لا ترسل السياق تظل ممكنة.
2. `OrdersService::createOrder` يحفظ `OrderAnalyticsContext` فقط إذا أرسل الطلب سياقًا، ويثبت `schema_version=2`. المخطط يتضمن app instance وsession والمنصة ولقطتي Branch؛ لا يحتوي `main_section`.
3. `AnalyticsOutboxService::record` يبني payload version 2 ويُنشئ سجلًا لكل من `ga4` و`branch` بمفتاح SHA-256 فريد. هذا يمنع تكرار **إنشاء صف outbox بالمفتاح نفسه**، ولا يضمن منع تكرار الحدث في GA4 أو Branch عند إعادة إرسال بعد استلام الطرف الآخر للطلب وقبل حفظ حالة النجاح.
4. `analytics:send-outbox` يرسل حتى 100 صف مستحق عبر HTTP مباشرة؛ لا يستخدم Queue Job. المجدول يطلب تشغيله كل دقيقة مع `withoutOverlapping(10)`. لا يثبت هذا أن cron/scheduler يعمل في الإنتاج.
5. لكل وجهة حالة منفصلة (`pending/sent/blocked/dead_letter`) مع إعادة محاولة تدريجية حتى 10 محاولات. لا يظهر مسار استعادة/إعادة تشغيل dead-letter في الكود المفحوص. استجابة HTTP ناجحة فقط تسجل `sent`؛ هذا ليس تحققًا من معالجة GA4 للحدث.

المراجع الأساسية: [AnalyticsOutboxService](../packages/core/orders/src/Services/AnalyticsOutboxService.php#L15)، [SendAnalyticsOutbox](../app/Console/Commands/SendAnalyticsOutbox.php#L15)، [مخطط outbox](../packages/core/orders/src/database/migrations/2026_10_12_140000_create_analytics_outbox_table.php#L11)، [جدولة الأمر](../routes/console.php#L28).

## 4. مطابقة الأحداث الـ31

**مفتاح التصنيف:** «جزئي» يعني أن مسارًا قريبًا أو اسمًا/شروطًا غير مطابقة موجودة؛ «غير موجود» يخص نطاق الكود المفحوص فقط. الإرسال الخلفي المكتشف يستهدف GA4 وBranch معًا؛ وجود الاستدعاء لا يثبت وصوله.

| # | الحدث المطلوب | الآلية والمسار الفعلي | الوجهة/البيانات/الاختبار | الحكم والدليل |
|---:|---|---|---|---|
| 1 | `view_item_list` | لا يوجد مصدر Flutter في المستودع | غير قابل للتحديد؛ لا اختبار عميل متاح | يعتمد على Flutter غير المتاح؛ لا يُعد غيابه هنا دليل عدم تنفيذ |
| 2 | `add_to_cart` | لا يوجد مصدر Flutter | غير قابل للتحديد | يعتمد على Flutter غير المتاح |
| 3 | `remove_from_cart` | لا يوجد مصدر Flutter؛ ورد اسم قديم في تقارير المستخدم لا في دليل كود قابل للتتبع | لا يمكن ربطه بإصدار أو وجهة | يعتمد على Flutter غير المتاح؛ يلزم فحص المصدر والتقرير حسب schema/version |
| 4 | `view_cart` | لا يوجد مصدر Flutter | غير قابل للتحديد | يعتمد على Flutter غير المتاح |
| 5 | `begin_checkout` | لا يوجد مصدر Flutter | غير قابل للتحديد | يعتمد على Flutter غير المتاح |
| 6 | `checkout_blocked` | لا يوجد مصدر Flutter | غير قابل للتحديد | يعتمد على Flutter غير المتاح |
| 7 | `add_payment_info` | لا يوجد مصدر Flutter | غير قابل للتحديد | يعتمد على Flutter غير المتاح |
| 8 | `order_created` | `OrdersService::createOrder` بعد إنشاء الطلب؛ يوجد أيضًا مسار `storeOrder` في الخدمة. API عميل يستدعي الإنشاء؛ سياق التتبع اختياري. | GA4 وBranch؛ `schema_version=2`, order/client identity, platform, attribution؛ session_id يرسل لهذا الحدث فقط. لا `main_section`. اختبار API/سياق فقط، لا اختبار إرسال. | منفذ جزئيًا؛ [OrdersService](../packages/core/orders/src/Services/OrdersService.php#L1096)، [outbox](../packages/core/orders/src/Services/AnalyticsOutboxService.php#L15) |
| 9 | `order_rescheduled` | لم يُعثر على استدعاء analytics مرتبط بتعديل الموعد | لا وجهة/حمولة؛ لا اختبار | غير موجود في نطاق الكود المفحوص؛ يحتاج تتبع كل مسارات إعادة الجدولة |
| 10 | `pickup_driver_assigned` | تعيين المندوب موجود تشغيليًا، لكن لا استدعاء outbox لهذا الحدث | لا وجهة/حمولة | غير موجود في نطاق الكود المفحوص |
| 11 | `pickup_started` | لا يوجد باعث بهذا الاسم. إشارة البدء الميداني للاستلام لا يمكن تقريرها من أسماء الحالات وحدها | لا وجهة/حمولة | غير موجود؛ الخطة تجعله مشروطًا بإشارة فعلية |
| 12 | `order_picked_up` | انتقال السائق إلى `order_has_been_delivered_to_admin` يطلق من Observer اسم `order_collected` | GA4 وBranch عبر outbox؛ بيانات أساسية للطلب دون `main_section` | منفذ بشكل مخالف في الاسم؛ [OrderObserver](../packages/core/orders/src/Observers/OrderObserver.php#L56)، [Driver OrderController](../packages/core/users/src/Controllers/Api/Driver/OrderController.php#L141) |
| 13 | `pickup_failed` | لم يُعثر على باعث؛ قد تسجل بعض الإخفاقات كحالة/ملاحظة دون حدث تحليلي | لا وجهة/حمولة | غير موجود في نطاق الكود المفحوص |
| 14 | `order_adjusted` | الإدارة تعدل الطلب/العناصر/الأسعار، لكن لا outbox لهذا الحدث | لا معرّف حركة مستقل ولا حمولة | غير موجود في نطاق الكود المفحوص؛ تكرار التعديلات يتطلب event id لكل تعديل |
| 15 | `order_priced` | التسعير يحدّث الطلب في الخدمة؛ لم يُعثر على إرسال تحليلي عند اعتماد السعر | لا وجهة/حمولة | غير موجود في نطاق الكود المفحوص |
| 16 | `payment_succeeded` | callback بوابة الدفع يسجل بعد نجاح المعاملة؛ source key بالمعاملة. | GA4 وBranch؛ النوع والقيمة/هوية المعاملة؛ التحقق من التطابق الفعلي يعتمد على إعداد الطرفين. لا إرسال لهذه الطبقة عند cash/wallet/points نفسها. | منفذ جزئيًا؛ [PaymentGatewayController](../packages/core/payment-gateways/src/Controllers/FrontEnd/PaymentGatewayController.php#L282) |
| 17 | `payment_failed` | callback الفشل يسجل حدثًا عند وجود طلب مرتبط | GA4 وBranch؛ قيمة المحاولة ونوعها؛ لا يغطي بالضرورة كل رفض/إلغاء قبل callback | منفذ جزئيًا؛ [PaymentGatewayController](../packages/core/payment-gateways/src/Controllers/FrontEnd/PaymentGatewayController.php#L352) |
| 18 | `delivery_driver_assigned` | لا استدعاء analytics وقت إسناد مندوب التوصيل | لا وجهة/حمولة | غير موجود في نطاق الكود المفحوص |
| 19 | `order_ready` | الحالة `ready_to_delivered` تحدث من Controller السائق، لكن الـObserver لا يربطها بحدث | لا وجهة/حمولة | غير موجود؛ [Driver OrderController](../packages/core/users/src/Controllers/Api/Driver/OrderController.php#L180) |
| 20 | `delivery_started` | الحالة `in_the_way` تحدث من السائق/الفني؛ لا اسم الحدث المطلوب في mapping | لا وجهة/حمولة | غير موجود؛ [OrderObserver](../packages/core/orders/src/Observers/OrderObserver.php#L54) |
| 21 | `delivery_arrived` | حالة `delivered` تطلق `order_delivered` | GA4 وBranch | منفذ بشكل مخالف: الاسم يوحي بالتسليم النهائي وفق الخطة لكنه يرسل عند وصول المندوب؛ [OrderObserver](../packages/core/orders/src/Observers/OrderObserver.php#L58) |
| 22 | `delivery_failed` | لا باعث تحليلي عُثر عليه | لا وجهة/حمولة | غير موجود في نطاق الكود المفحوص |
| 23 | `order_delivered` | لا إرسال عند انتقال `finished`; الاسم يُستخدم حاليًا عند `delivered` | GA4 وBranch لكن عند المرحلة الخطأ | منفذ بشكل مخالف للتوقيت؛ [Driver OrderController](../packages/core/users/src/Controllers/Api/Driver/OrderController.php#L331)، [OrderObserver](../packages/core/orders/src/Observers/OrderObserver.php#L58) |
| 24 | `purchase` | ينشأ عند دفع كامل إجمالي الطلب في Observer معاملات، وعند callback الدفع إذا وصل المدفوع للإجمالي؛ ويوجد مسار `storeOrder` قد ينشئه مبكرًا. لا شرط `finished` أو إقفال القيم. | GA4 purchase وBranch standard PURCHASE. Outbox unique على الوجهة/المصدر/الطلب/الحدث. payload من القطع والخصم/الضريبة/التوصيل. | منفذ بشكل مخالف للتوقيت؛ [OrderTransactionObserver](../packages/core/orders/src/Observers/OrderTransactionObserver.php#L80)، [PaymentGatewayController](../packages/core/payment-gateways/src/Controllers/FrontEnd/PaymentGatewayController.php#L319)، [PurchasePayloadBuilder](../packages/core/orders/src/Services/PurchasePayloadBuilder.php#L15) |
| 25 | `order_cancelled` | Observer يسجل عند تغير status إلى `canceled`، مع سبب ضمن قائمة مسموحة | GA4 وBranch؛ سبب cancellation؛ لا يغطي الاسم الداخلي `cancelled` إن استُخدم بدل `canceled` | منفذ جزئيًا؛ [OrderObserver](../packages/core/orders/src/Observers/OrderObserver.php#L59) |
| 26 | `refund` | توجد أرصدة/نقاط تعاد عند الإلغاء أو تعديل المبلغ، لكن لا event `refund` | لا وجهة/حمولة أو ربط باسترداد دفع فعلي | غير موجود في نطاق الكود المفحوص |
| 27 | `wallet_recharged` | callback الدفع المنفذ لشحن المحفظة لا يصدر حدثًا تحليليًا باسم الشحن | GA4/Branch غير مستدعاة لهذا المسار | غير موجود؛ لا تخلط الشحن مع purchase |
| 28 | `wallet_credit_issued` | إنشاء رصيد ترويجي/تعويض/كاش باك/مكافأة عبر ledger موجود في مسارات متعددة، لكن لا باعث تحليلي موحد | لا فصل تحليلي funded/promotional ولا معرف حملة/دفعة ضمن outbox | غير موجود في نطاق التتبع؛ حركة ledger قد تحدث رغم غياب الحدث |
| 29 | `wallet_credit_expired` | يوجد حقل `expired_at` وبعض أنواع `expiry_deduction` في ledger، ولم يُعثر على event expiry | لا وجهة/حمولة | غير موجود؛ يتطلب فحص job الانتهاء وتشغيله على بيانات آمنة |
| 30 | `loyalty_earned` | نقاط الإحالة/الإنفاق تُمنح في Controllers بعد الإكمال، بلا outbox | لا وجهة/حمولة؛ يمكن أن تتغير أرصدة النقاط دون حدث | غير موجود في نطاق الكود المفحوص |
| 31 | `loyalty_redeemed` | إنشاء Point من نوع withdraw عند الطلب موجود، ولا إرسال تحليلي | لا وجهة/حمولة؛ لا اختبار تكامل تحليلي | غير موجود في نطاق الكود المفحوص |

**اختبارات الكود:** يوجد `tests/Feature/AnalyticsContextValidationTest.php` للتحقق من اختيارية سياق الطلب وشكله، كما يوجد فحص سياق في اختبار إنشاء منتج/طلب. لم يُعثر على اختبارات مخصصة لحمولة Outbox، توقيت Observer، مبلغ purchase، HTTP payload لـGA4/Branch، retries أو تطابق الأحداث الـ31. لم تُشغّل أي اختبارات في هذا التدقيق.

## 5. النتائج حسب الأولوية

### P0 — تثبيت معنى `purchase` وتوقيته

- **المشكلة والدليل:** الشراء ينشأ بمجرد بلوغ `paid >= total_price` في Observer المعاملات، وكذلك في callback الدفع، ومسار الطلب اليدوي `storeOrder`؛ لا ينتظر حالة `finished` ولا تسوية/قفل قيم الطلب. هذا يخالف قاعدة الخطة التي تعتبر `purchase` بعد التسليم النهائي، مرة منطقيًا واحدة.
- **المسار/الأثر:** طلب مدفوع مقدمًا قد يظهر كشراء قبل التنفيذ أو قبل تثبيت القطع النهائية؛ قد يسجل `purchase` لا يمكن تحديث قيمه لاحقًا. مفتاح idempotency نفسه قد يحمي من صف ثانٍ مطابق، لكن لا يعالج التوقيت ولا حمولات الالتقاط المبكر.
- **الحالة:** مؤكد بالكود؛ وقوعه وعدده في الإنتاج يحتاج outbox وبيانات الطلب.
- **المعالجة المقترحة:** جعل حدث الشراء مشروطًا بانتقال نهائي موثوق بعد التسوية وتجميد snapshot النهائية، مع قيد منطقي لكل order ووجهة. إبقاء الدفع كحدث مستقل، وعدم توليد purchase من شاشة النجاح أو شحن المحفظة.
- **قبول:** حالات الدفع الكامل قبل `finished` لا تنشئ purchase؛ عند انتقال واحد إلى `finished` بعد التسوية ينشأ payload نهائي واحد؛ replay لا يضاعف الصف المنطقي؛ الطلب غير المسعّر لا يحمل قيمة مختلقة.

### P0 — تصحيح مطابقة الحالات

- **المشكلة والدليل:** `order_has_been_delivered_to_admin` يرسل `order_collected`، `delivered` يرسل `order_delivered`، أما `ready_to_delivered`, `in_the_way`, `finished` فلا تطلق الأحداث المطلوبة. Controller السائق يغير الحالات صراحةً في الأسطر 141 و180 و218 و255 و331.
- **الأثر:** التقارير تجمع وصول السائق تحت حدث التسليم النهائي، ولا ترى بدء التوصيل/جاهزية الطلب/إتمامه وفق الخطة. هذا تفسير كودي مباشر لاختلاف الاسم عن `order_picked_up` الوارد في التقرير.
- **المعالجة:** توحيد mapping على قيم الحالة الداخلية لا labels لوحة الإدارة؛ إضافة انتقالات التسليم/الاستلام فقط بعد تحديد الحالة/الفعل المؤكد؛ ربط كل انتقال بمصدر واحد مشترك يغطي الإدارة وAPI والتطبيقات.
- **قبول:** اختبار كل حالة من الجدول في كل مسار تعديل؛ حدث واحد بالاسم والتوقيت الصحيح؛ لا حدث عند تحديث غير متعلق بالحالة ولا تكرار عند إعادة الحفظ.

### P1 — بيانات الإصدار والقسم وهوية التطبيق

- **المشكلة:** outbox يضيف `schema_version=2` لكن لا `main_section`; جدول context كذلك لا يحتوي القسم. سياق التطبيق اختياري. GA4 يرفض/يحجب سجلًا إذا نقص platform أو app_instance_id أو إعداد stream، في حين Branch يحتاج هوية المطور ومفتاح المنصة.
- **الأثر:** يتعذر فصل نسخة الخطة والقسم، وقد تبقى السجلات `blocked` عند طلب لوحة الإدارة/عميل قديم/إعداد غائب. قبول HTTP 2xx لا يضمن ظهور GA4.
- **الدليل:** `AnalyticsOutboxService.php:21–34`; `SendAnalyticsOutbox.php:34–52`; `CreateOrderRequest.php:191–208`; migration سياق الطلب `:11–24`.
- **المعالجة:** إضافة قيمة القسم الثابتة ومصدر موحد للسياق حسب العقد، وتحديد سياسة آمنة للطلبات غير المنسوبة بدل اختراع instance id؛ توثيق أسماء متغيرات إعداد GA4/Branch في ملف config. تحقق لوحة GA4/Branch بعد ذلك.
- **قبول:** payload version 2 يحمل القسم الصحيح؛ request قديم لا يفشل لكنه يصنف واضحًا؛ سجل blocked يعطي سببًا دون كشف بيانات؛ platform/app id يوجهان للتطبيق الصحيح.

### P1 — Outbox موثوق ذريًا وقابل للاستعادة

- **المشكلة:** يوجد Outbox وunique key، لكن استدعاءات التسجيل قد تقع داخل/حول معاملات الطلب أو webhook، ولا يظهر ضمان after-commit موحد في الخدمة. قد يبقى الحدث بعد rollback في مسارات بلا transaction خارجية أو لا يظهر إذا التقط الاستثناء قبل insert. لا يوجد lease/lock داخل المرسل ولا استعادة dead-letter موثقة.
- **الأثر:** صف يتيم أو نقص حدث إذا فشل المسار، وازدواج إرسال عند توقف العملية بين استجابة الطرف الخارجي وتسجيل `sent`. جدولة overlap تمنع تداخل الأمر المجدول فقط، لا التشغيل اليدوي/عملية ثانية على مضيف آخر.
- **الدليل:** `AnalyticsOutboxService.php:15–75`; `SendAnalyticsOutbox.php:15–120`; migration outbox `:11–26`; scheduler `routes/console.php:28`.
- **المعالجة:** حفظ outbox ضمن معاملة العملية المالية نفسها وبـafter-commit dispatch semantics، claimed/lease atomic للصف، سياسة retries/dead-letter مع command استرداد محكوم، ومطابقة لاحقة مع provider. اعتبر unique داخليًا ضمان إدراج فقط.
- **قبول:** rollback لا يترك صفًا؛ فشل طرف لا يمنع العملية المالية؛ عاملان لا يعالجان الصف نفسه؛ انقطاع بعد 2xx يعالج بتصميم يمنع/يكشف الازدواج؛ dead-letter قابلة للفحص وإعادة الإرسال المدققة.

### P1 — ربط payload الشراء بالتسوية والقيم النهائية

- **المشكلة:** `PurchasePayloadBuilder` يتحقق من وجود القطع ومطابقة مجموعها مع `order_price` ضمن هللتين، ويطبق coupon/ضريبة 15% والتوصيل. لكنه يشتق tax من إجمالي القطع بعد الخصم، ويقارن القيمة مع `total_price`; لا يوجد snapshot عند `finished` ولا معالجة لاسترداد/تعديل لاحق أو bag_overage مخصص واضح في الكود المقروء. الكاش باك يُعامل كخصم صفر ويصدر لاحقًا كرصيد، وهو فصل مناسب مبدئيًا، لكن المبلغ الترويجي لا يمر إلى payload كحركة مكتسبة.
- **مثال حسابي:** 10 × 10 ريال شامل الضريبة = إجمالي 100.00؛ الصافي قبل الضريبة `100/1.15 = 86.9565…` ويقرب 86.96؛ الضريبة 13.04؛ المجموع 100.00. حاصل سعر الوحدة الصافي المقرب إلى 6 منازل × 10 = 86.95652، فرق 0.00348 ريال عن القيمة الإجمالية المقربة، أي أقل من هللة لهذا المثال. هذا لا يثبت أن كل توزيع خصم/عدد أسطر يفي بالحد؛ يلزم اختبار حالات متعددة على بيانات مولدة معزولة.
- **المعالجة:** اعتماد مصدر نهائي واحد لقفل القطع والضرائب والخصومات والتوصيل؛ تمثيل الحقيبة والزيادات وفق العقد؛ اختبارات حسابية متعددة للخصم النسبي والكميات، لا تغيير سياسة ضريبية أثناء الإصلاح.
- **قبول:** `value + tax + shipping` يساوي إجمالي الفاتورة ضمن سياسة التقريب المحددة؛ قيمة العناصر تطابق net services؛ تتغير القطع قبل الإغلاق وتثبت بعده؛ لا قيمة شراء على الدفعات الجزئية.

### P1 — تغطية رصيد المحفظة والحملات والنقاط

- **المشكلة:** الـledger موجود ويستخدم `transaction_type` مثل `charge`, `promotional_add`, `cashback`, `reward`, `compensation_add`, `remaining_amount`, `expiry_deduction`; لكن لا استدعاءات analytics لـ`wallet_recharged`, `wallet_credit_issued`, `wallet_credit_expired`, `loyalty_earned`, `loyalty_redeemed`. لا يوجد تصنيف `funded` مقابل `promotional` في payload الموجود.
- **الأثر:** لا يمكن وصل حملتي A وB أو كود cashback في C مع أحداث الإصدار/الاستهلاك من تقرير Analytics؛ وهذا لا يعني أن منح الرصيد لم يحدث ماليًا. نوع الحركة قد يتيح إعادة بنائها من ledger لكن ليس event attribution أو cohort بدقة.
- **المعالجة:** أحداث مستقلة عند إنشاء ledger/استهلاكه/انتهائه، مفاتيح حركة ledger ثابتة، حقول funding class وحملة/دفعة اختيارية، وعدم إطلاق purchase لشحن المحفظة. حسم سحب الرصيد الترويجي والممول عند الخصم حسب ترتيب ledger المالي الفعلي.
- **قبول:** كل حركة رصيد تنتج الحدث المطابق مرة واحدة؛ charge ممول لا يساوي issued ترويجي؛ refund/reversal يعكس الأصل؛ انتهاء الصلاحية يسجل الحركة الفعلية لا مجرد تاريخ الانتهاء.

### P2 — استكمال التغطية للعمليات الإدارية والسريعة والكوبونات

- **المشكلة:** لم يوجد باعث عام لـ`order_rescheduled`, assignment, `order_adjusted`, `order_priced`, failures, `refund`. الخدمة تتحقق من كوبون عبر `Coupon::findMatching` في بعض المسارات وتحسب percentage/value/cashback والحد الأدنى والتوصيل، لكن سجلات التحليلات لا تحفظ سبب قبول/رفض أو كود/حملة. السياق لا يحمل campaign_id/batch_id، وطلب العميل لا يضم هذه المفاتيح.
- **الأثر:** لا يمكن استنتاج فشل coupon أو نسبة استعمال الأكواد من Analytics الحالي، ولا ربط حملة بلا كود باستخدام رصيدها. المسارات العادية والسريعة والإدارية تختلف؛ لا دليل Flutter لإثبات عرض رسائل الرفض.
- **المعالجة:** جرد موحد لمسارات إنشاء وتعديل وتسعير الطلب، تحديد event لكل تغيير مع source id، توثيق contract لكل coupon type (الخصم مقابل cashback)، إضافة campaign attribution حيث يلزم مع المحافظة على قواعد الصلاحية الحالية.
- **قبول:** حالات حد أدنى/سقف/صلاحية/أهلية/نوع طلب تمر في اختبارات معزولة؛ event يسجل تطبيق الخصم فقط عند قبول فعلي؛ سبب الرفض واضح للتطبيق؛ طلب سريع قبل التسعير لا يسجل مبلغًا نهائيًا.

### P2 — حماية البيانات والهوية وتوثيق الاختبارات

- **المشكلة:** Branch يستخدم `developer_identity` من client id الداخلي، بينما GA4 يستخدم `app_instance_id`، وsession يضاف إلى `order_created` فقط. لا يظهر في payload فصل واضح بين معرّف المستخدم التحليلي ومعرّف عميل قاعدة البيانات أو سياسة مدة الجلسة. الاختبارات لا تغطي الإرسال.
- **المعالجة:** مراجعة سياسة الخصوصية/هوية Branch وGA4، تجنب إرسال معرفات داخلية مباشرة ما لم تكن متوافقة ومقصودة، واختبارات fake HTTP لكل منصة مع الحد الأدنى من البيانات.
- **قبول:** مراجعة payloads لا تحتوي PII أو معرفات محظورة؛ كل provider له identity field مطابق لعقده؛ اختبارات request وretry وstatus مستقلة.

## 6. مبالغ الشراء والدفع: ما أمكن إثباته

- `PurchasePayloadBuilder` يقرأ عناصر الطلب المحفوظة، ويوقف payload عند غياب العناصر، فرق مجموع العناصر عن `order_price` أكبر من `0.02`، خصم غير صالح، كمية غير صالحة، أو عدم مصالحة الإجمالي. يوزع الخصم تناسبيًا على الأسطر ويضع المتبقي على السطر الأخير؛ سعر الوحدة يحتفظ بست منازل.
- حمولة الشراء تشمل `value`, `tax`, `shipping`, `items`, `wallet_paid_amount`, `wallet_promo_amount=0`, `loyalty_points_used`. الصفر الثابت للجزء الترويجي لا يمثل مصدر تمويل المحفظة؛ وقد لا يكفي حقل order وحده للفصل.
- callback بوابة الدفع يسجل قيمة المعاملة كـ`payment_succeeded`/`payment_failed`، وهو أفضل من تسجيل كامل الطلب كقيمة كل دفعة. purchase نفسه مشروط حاليًا بوصول المدفوع للإجمالي، لا بإنهاء التسليم.
- لم يظهر تتبع مركزي مؤكد لمحاولات الدفع النقدي أو wallet/points كـ`payment_succeeded`. لم يمكن تأكيد تغطية Apple Pay مقابل البطاقة من الكود وحده دون معرفة طرق gateway المستخدمة في إعداد التشغيل. لا تساوي `pay_on_delivery` مع وسيلة دفع ناجحة.
- الطلب السريع/غير المسعّر قد يُحجب إذا لم تتطابق العناصر والإجمالي؛ الكود لا يرسل قيمة افتراضية في builder. يلزم التحقق من outbox لمعرفة هل هو `blocked` أو لم يُنشأ.

## 7. ما لا يمكن إثباته من المستودع

1. **الإصدار المنشور:** لا يوجد رابط موثوق بين SHA أعلاه ونسخة الإنتاج. يلزم commit/tag build في pipeline أو رقم release من الخادم، دون مشاركة أسرار.
2. **GA4/Branch:** أسماء config هي `GA4_FIREBASE_APP_ID_IOS`, `GA4_API_SECRET_IOS`, `GA4_FIREBASE_APP_ID_ANDROID`, `GA4_API_SECRET_ANDROID`, `BRANCH_KEY_IOS`, `BRANCH_KEY_ANDROID`. وجود الاسم في `.env` لا يثبت أن القيمة صحيحة أو محملة. يلزم فحص حالة وجود/تاريخ تحديث القيم من مسؤول التشغيل دون عرض القيم، ثم سجلات outbox ومنصات القياس.
3. **Scheduler/worker:** `routes/console.php` يحدد الجدولة، لكن لا دليل على cron/schedule:run أو الأذونات أو تعدد المضيفين أو طول عمر worker في الإنتاج.
4. **التقارير المجمعة:** أحداث 1–19 أكتوبر تتداخل قبل/بعد تحديث التطبيق ولا تتضمن `schema_version`; لا تنسب الزيادة للتحديث أو الحملة منفردة. اختلاف الأسماء مثل `order_collected` له تفسير كودي مباشر؛ غياب بقية الأحداث قد يكون code أو blocked config أو scheduler أو تقرير/فلتر.
5. **Flutter:** لا مصدر عميل لفحص أحداث التصفح أو إرسال سياق app instance/session/Branch أو عقد coupons أو تعامل الأخطاء. الأسماء القديمة في Analytics لا تثبت أي مصدر أو إصدار بعينه.
6. **Campaigns/ledger:** لا بيانات campaign A/B/C ولا صفوف ledger إنتاجية ضمن الفحص. غياب `wallet_credit_issued`/`loyalty_earned` من التقرير لا يثبت غياب الحركة المالية.
7. **GA4:** Custom Definitions وKey Events وDebugView/BigQuery وربط App Streams لا يمكن إثباتها محليًا. كذلك 2xx من Measurement Protocol لا يثبت قبول كل parameter أو ظهوره في التقرير.

## 8. تحقق تشغيل آمن مقترح (لا يُشغّل هنا)

نفّذها لاحقًا على نسخة قراءة فقط أو بيئة Staging، ولا تستخرج أسماء أو هواتف أو عناوين العملاء. هذه أمثلة SQL استكشافية على أسماء الجداول والأعمدة الموجودة في migrations؛ عدّل dialect حسب قاعدة البيانات، وأبقِ النتائج تجميعية.

### أ. ملخص حالات outbox حسب الحدث والوجهة

```sql
SELECT event_name, destination, status, COUNT(*) AS rows_count,
       MIN(created_at) AS first_row, MAX(created_at) AS last_row
FROM analytics_outbox
WHERE created_at >= '2026-10-01' AND created_at < '2026-10-20'
GROUP BY event_name, destination, status
ORDER BY event_name, destination, status;
```

`blocked` مع نقص إعداد/هوية يوجه إلى config أو سياق الطلب؛ `pending` القديم يشير إلى scheduler/HTTP/retry؛ `dead_letter` يحتاج فحص last_error بعد تنقيحه من أي بيانات حساسة؛ غياب الحدث كله قد يعني عدم إنشاء صف أو مسار مختلف.

### ب. مطابقة مبدئية للشراء مع حالة الطلب

```sql
SELECT o.status, ao.status AS outbox_status, COUNT(DISTINCT o.id) AS orders_count
FROM orders o
LEFT JOIN analytics_outbox ao
  ON ao.order_id = o.id AND ao.event_name = 'purchase'
WHERE o.created_at >= '2026-10-01' AND o.created_at < '2026-10-20'
GROUP BY o.status, ao.status
ORDER BY o.status, ao.status;
```

التوقع المستقبلي: purchases المنطقية ترتبط بالطلبات ذات `finished` بعد التسوية. الوضع الحالي قد يظهر purchase مع حالة أخرى بسبب شرط الدفع الكامل؛ استخرج IDs فقط داخل بيئة مؤمنة للتحقيق ولا تشاركها بالتقرير.

### ج. دفعات بوابة الدفع وأحداثها

ابدأ باستكشاف schema الفعلي لـ`payment_transactions`/جدول المعاملات داخل بيئة Staging قبل كتابة الربط؛ اسم جدول المعاملات وأعمدته لم يُثبت هنا بالـmigration. طابق فقط transaction ids داخليًا ثم أخرج counts حسب status ووجود `payment_succeeded/failed` في outbox، دون إظهار ids أو PII.

### د. الحركات المالية والترويجية والنقاط

استكشف schema الفعلي لـ`wallet_transactions` و`points` أولًا (المصدر يثبت وجود transaction_type/amount/expired_at وعمليات النقاط، لكنه لا يثبت كل أسماء الأعمدة الحالية). أخرج counts مجمعة حسب نوع العملية والفترة والحملة إن وجد حقل صالح؛ قارن مع outbox events. لا تفسر عدم التطابق كعدم وقوع الحركة حتى تراجع مسارات ledger.

### هـ. التحقق من التطبيقات والمنصات

- من GA4: افصل App streams وفلتر `schema_version=2` و`main_section=laundry_station` بعد تسجيلهما كـcustom dimensions؛ انتبه لحدود retention وتأخر المعالجة.
- من BigQuery: احسب العد الخام، ثم العد المنطقي بعد إزالة التكرار بمفتاح ثابت موثق (لأن وجود event key في قاعدة التطبيق لا يزيل تكرارات GA4). قارن by event/platform/day، لا بالفترة الكلية فقط.
- من Branch: افحص custom `ORDER_CREATED` وstandard `PURCHASE`، هوية العميل، event transaction identity وattribution من لوحة الاختبار؛ يلزم التحقق من إعداد Branch وربطه بمنصة الإعلان، وليس الكود وحده.
- من Scheduler: تحقق من نجاح `schedule:run` ووجود تنفيذ `analytics:send-outbox` والعدد المرسل/المتوقف، بلا تشغيل إعادة إرسال أو replay أثناء التشخيص.

## 9. ما نُفذ جيدًا

- وجود جدول سياق مستقل عن الطلب واحتفاظ بلقطتي Branch first/latest ومفتاح schema version.
- `analytics_context` اختياري ومحدد الحقول، وهو مناسب للتوافق مع clients الأقدم.
- Outbox يفصل حالة GA4 عن Branch ويستخدم مفتاحًا فريدًا لكل event/destination/source/order.
- استثناء فشل التحليلات عن مسار تجربة الطلب مقصود في service؛ يحد من جعل تعطل مزود التحليلات يعطل الطلب.
- بناء purchase من عناصر محفوظة مع تحقق للمجاميع والخصم والكمية ووقف السجلات غير الصالحة بدل إرسال سعر مختلق.
- الدفع الناجح في callback يسجل مبلغ المعاملة وليس كامل قيمة الطلب في حدث payment.
- Observer يقيّد أحداث الحالات بالتغير الفعلي للـstatus، ويقصر سبب الإلغاء على قائمة خيارات.

هذه نقاط تنفيذ داخل المستودع وليست إثباتًا لوصول الأحداث أو سلامة إعدادات الإنتاج.

## 10. خطة معالجة مرحلية وتقدير تقريبي

| المرحلة | العمل | الاعتماديات | تقدير الجهد |
|---|---|---|---|
| 0 — تثبيت العقد | استعادة DOCX الأصلي إن توفر؛ اعتماد أسماء الحالات والأحداث وحقول section/identity/funding/campaign؛ تحديد سياسة event IDs | مالك المنتج والتحليلات، مطورو التطبيق والباك إند | 0.5–1 يوم |
| 1 — Backend order lifecycle | mapping موحد لكل تغييرات الحالة؛ إضافة reschedule/assignment/adjust/priced/failure/refund؛ ربط `purchase` بـ`finished` بعد التسوية والإغلاق؛ ضمان after-commit/unique منطقي | المرحلة 0، جرد controller/jobs/bulk updates | 3–6 أيام |
| 2 — Backend wallet/loyalty/coupon | تحديد ledger funded/promotional؛ إصدار/انتهاء/استرداد الرصيد؛ كسب/استخدام النقاط؛ حملة/دفعة؛ coupon accepted/rejected مع أسباب غير شخصية | مراجعة مخطط ledger وقواعد المالية كما هي | 3–5 أيام |
| 3 — Reliability/tests | claim/lease للـoutbox، retries وdead-letter recovery مراقبة، اختبارات unit وfeature باستخدام fake HTTP وقاعدة اختبار، اختبارات تقريبات متعددة | المرحلتان 1–2، بيئة اختبار منعزلة | 2–4 أيام |
| 4 — Flutter | إضافة أحداث التصفح السبعة، ضبط context وإرساله مع الطلب، التحقق من coupon UX والتوافق، اختبار iOS/Android | مستودع Flutter غير متاح هنا؛ عقد Backend مستقر | 3–7 أيام لكل دورة إصدار/QA |
| 5 — GA4/Branch/BigQuery/تشغيل | تعريف أبعاد القسم/schema، ضبط Key Events عند الحاجة، تأكيد App IDs/Secrets/Branch keys بطريقة سرية، مراجعة scheduler، لوحات أخطاء ومصالحة dedup | release منشور ومرحلة 1–4 | 1–3 أيام إعداد، ثم مراقبة 3–7 أيام |

التقديرات لشخص/فريق صغير، وتتغير حسب عدد مسارات التعديل والتطبيقات وحجم اختبارات QA. لا تتضمن نشرًا أو تعديلًا ماليًا مباشرًا.

## 11. فهرس الملفات المحورية

- `config/analytics.php` — مصادر أسماء إعدادات GA4/Branch.
- `routes/console.php` — تعريف مواعيد command والـqueue worker.
- `packages/core/orders/src/Requests/Api/CreateOrderRequest.php` — عقد سياق analytics الاختياري.
- `packages/core/orders/src/Services/OrdersService.php` — إنشاء وتعديل وتسعير الطلب والكوبون والخصم والpoints/wallet.
- `packages/core/orders/src/Services/AnalyticsOutboxService.php` — payload ومفاتيح outbox.
- `packages/core/orders/src/Services/PurchasePayloadBuilder.php` — حساب payload الشراء والتحقق.
- `packages/core/orders/src/Observers/OrderObserver.php` — أحداث تغير status.
- `packages/core/orders/src/Observers/OrderTransactionObserver.php` — purchase المستنتج من المعاملات.
- `packages/core/payment-gateways/src/Controllers/FrontEnd/PaymentGatewayController.php` — callbacks وإرسال payment/purchase.
- `app/Console/Commands/SendAnalyticsOutbox.php` — HTTP clients والـretry والحالة.
- `packages/core/orders/src/database/migrations/2026_10_12_130000_create_order_analytics_contexts_table.php` — schema context.
- `packages/core/orders/src/database/migrations/2026_10_12_140000_create_analytics_outbox_table.php` — schema outbox.
- `packages/core/users/src/Controllers/Api/Driver/OrderController.php` و`packages/core/users/src/Controllers/Api/Technical/OrderController.php` — مسارات إكمال الطلب ومكافآته.
- `packages/core/wallet/src/Services/WalletTransactionsService.php` و`packages/core/wallet/src/Models/WalletTransaction.php` — ledger والمحفظة.
- `tests/Feature/AnalyticsContextValidationTest.php` — اختبارات سياق الطلب فقط.

## 12. الخلاصة

في حدود `main@a64a45c6c58e72ca2afe8546dcd08561638aa7e4`، الكود **لا يطابق الخطة بالكامل**. المطابقة جزئية في سياق الإصدار وOutbox وبيانات purchase الأساسية وcallbacks الدفع؛ الخلل الأوضح المؤكد هو اختلاف أسماء/توقيت أحداث الحالات و`purchase` المبكر، وغياب `main_section` وتتبع ledger للمحفظة/النقاط. أحداث Flutter وإعدادات التشغيل ووصول GA4/Branch تحتاج أدلتها خارج هذا المستودع. أُنشئ هذا التقرير فقط؛ لم تُنفذ إصلاحات أو أوامر تشغيلية.

## ملحق تحديث التنفيذ — 2026-10-23

هذا الملحق أحدث من نتائج التدقيق أعلاه ويغطي التغييرات الموجودة حاليًا في working tree؛ الجداول التفصيلية السابقة تسجل حالة الفحص قبل تنفيذ هذه التغييرات. راجع [حالة التنفيذ والقرارات](analytics/IMPLEMENTATION_STATUS_2026-10-23.md) للتفاصيل.

أضيفت في الباك إند معالجة اختيارية ومتوافقة مع العملاء السابقين لـ`app_version`، وربط إضافي لأحداث الطلب والدفع والمحفظة والنقاط والكوبونات وإعادة الجدولة، وفصل ledger للمحفظة إلى `funded` و`promotional` و`unclassified` مع تعديل يدوي إداري. كما أضيف منح النقاط بعد `finished` دون انتهاء صلاحية للنقاط، وبقي انتهاء صلاحية رصيد المحفظة قائمًا. قيمة `eligible_amount` تتبع قرار المنتج المسجل في وثيقة الحالة.

ما زال التحقق غير مكتمل: لا يوجد مصدر Flutter في هذا المستودع، ولم يُشغّل PHP lint لأن PHP CLI غير متاح في بيئة العمل، ولم تُشغّل اختبارات أو migrations. إعدادات GA4/Branch/BigQuery والإنتاج تحتاج تحققًا مستقلًا. لا تعتبر هذه التغييرات جاهزة للنشر قبل مراجعة Staging واختبارات القبول.
