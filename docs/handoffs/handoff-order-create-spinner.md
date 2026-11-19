# تسليم عاجل: زر إتمام الطلب يعلق على Spinner والطلب يكون قد تَسَجَّل

تاريخ: 2026-11-19  
الأولوية: عالية (مشكلة إنتاج حالية)  
المشروع:
`clean-station-flutter`  
الجهة المستلمة: مبرمج الموبايل  
الهدف: إصلاح ورفع نسخة جديدة للمتاجر في أسرع وقت

---

## 1) ماذا يرى العميل؟

يظهر بنفس الشكل على المسارين:

- ادفع عند التسليم (كاش)
- ادفع الآن / أونلاين (بطاقة)

التسلسل:

1. يفتح شاشة
مراجعة الطلب
2. يضغط زر إتمام الطلب
3. الزر يتحول إلى
spinner
وينقفل (ما ينفع ينضغط مرة ثانية) — هذا سلوك صحيح للزر
4. الشاشة تضل تلف
5. الطلب في الحقيقة يكون اتسجل على السيرفر ويظهر في لوحة التحكم
6. في مسار الأونلاين: مودال/
WebView
الدفع **ما بيفتح** رغم أن السيرفر جهّز
`payment_url`
7. بعض العملاء يقفلوا التطبيق ويفتحوه فيلاقوا الطلب
8. بعض الحالات يتسجل طلبين بنفس البيانات برقمين مختلفين (محاولة إنشاء ثانية بعد ما الأولى نجحت بدون ما التطبيق يعرف)

تم التحقق ميدانياً: طلبنا من عميل إنشاء طلب → الطلب ظهر في الأدمن والـ
spinner
لسا لفّان عنده.

---

## 2) السبب الجذري (مؤكد من كود الموبايل)

المشكلة **ليست** من جودة الإنترنت عند العميل، و**ليست** ضغط مرتين على الزر.

المشكلة في ترتيب التنفيذ بعد نجاح إنشاء الطلب:

الملف:

`lib/screens/client/bill/bloc/create_order_bloc.dart`

الدالة:

`_fCreateOrder`

التسلسل الحالي الخاطئ:

1. إرسال
`POST client/orders`
2. السيرفر ينجح ويسجّل الطلب ويرجع
`success`
مع
`data.id`
3. التطبيق يعمل
`await`
لكتابة تتبع على
Firebase Realtime Database
4. **فقط بعد انتهاء Firebase** يتم
`emit(DoneCreateOrderState(...))`
الذي يوقف الـ
spinner
وينقل لشاشة النجاح **أو يفتح مودال الدفع للأونلاين**

مهم لمسار
ادفع الآن
: فتح
`WebViewPayment`
يحصل داخل
`doneCreatedOrderFunc`
بعد
`DoneCreateOrderState`
فقط. إذا Firebase علّق، المودال لا يفتح حتى لو
`payment_url`
راجع من السيرفر.

الكود الحالي:

```dart
CustomResponse response = await dioHelper.sendToServer(
  url: "client/orders",
  body: body,
);
if (response.success) {
  try {
    await TrackingOrderFbDB.instance.onCreateOrder(
      orderDetails: event.inputData.toJson(),
      paymentType: event.inputData.staticPaymentTypeModel?.backEndKey ?? '',
      serverStatus: true,
      serverResponse: response.response?.data['data'] ?? {},
      orderId: '${response.response?.data["data"]["id"]}',
    );
  } catch (e) {
    log('Firebase tracking failed, continuing without tracking');
  }

  emit(
    DoneCreateOrderState(
      msg: response.msg,
      response: '${response.toMap()}',
      orderId: '${response.response?.data["data"]["id"]}',
      paymentUrl: "${response.response?.data['data']['payment_url']}"
    ),
  );
}
```

ودالة التتبع:

الملف:

`lib/services/business/track_create_order/track_create_order_service.dart`

```dart
await newOrderRef.set(orderData); // بدون timeout
```

### لماذا هذا يسبب التعليق؟

- كتابة
Firebase
ما عندها
timeout
- إذا
Firebase
علّق وما رجّع لا نجاح ولا Exception، الـ
`catch`
ما بيشتغل
- `DoneCreateOrderState`
ما بينبعث
- الـ
spinner
يضل ظاهر للأبد
- الطلب أصلاً موجود على السيرفر

هذا يفسر كمان ليش عند بعض المطورين/الأجهزة ما بتظهر المشكلة: عندهم
Firebase
بيرد بسرعة. وعند بعض العملاء بالسعودية بيعلق أحياناً (خدمة Google/Firebase مستقلة عن سرعة التصفح).

---

## 3) الحل المطلوب (ملزم)

### القاعدة

نجاح إنشاء الطلب على السيرفر = انتقال فوري لشاشة النجاح.  
أي تتبع جانبي (Firebase / Analytics) **لا يؤخر** إتمام الطلب.

### التعديل الأساسي في `_fCreateOrder`

1. بعد
`response.success`
ابعث
`DoneCreateOrderState`
فوراً
2. تتبع
Firebase
إمّا:
   - بدون
`await`
(خلفية)، أو
   - مع
`timeout`
قصير (2–3 ثوانٍ) ثم تجاهل الفشل
3. لا تسمح لفشل/تعليق التتبع أن يمنع الانتقال لشاشة النجاح

مثال اتجاه مقبول:

```dart
if (response.success) {
  final orderId = '${response.response?.data["data"]["id"]}';
  final paymentUrl = response.response?.data['data']['payment_url']?.toString();

  emit(
    DoneCreateOrderState(
      msg: response.msg,
      response: '${response.toMap()}',
      orderId: orderId,
      paymentUrl: paymentUrl,
    ),
  );

  // التتبع لا يوقف تجربة المستخدم
  unawaited(() async {
    try {
      await TrackingOrderFbDB.instance
          .onCreateOrder(
            orderDetails: event.inputData.toJson(),
            paymentType:
                event.inputData.staticPaymentTypeModel?.backEndKey ?? '',
            serverStatus: true,
            serverResponse: response.response?.data['data'] ?? {},
            orderId: orderId,
          )
          .timeout(const Duration(seconds: 3));
    } catch (e, st) {
      log('Firebase tracking skipped/failed: $e\n$st');
    }
  }());
} else {
  emit(
    FailedCreateOrderState(
      msg: response.msg,
      errType: response.errType ?? 0,
      response: '${response.toMap()}',
    ),
  );
}
```

ملاحظات:

- أضف
`import 'dart:async';`
إذا لزم لـ
`unawaited`
- أو استخدم
`.ignore()`
حسب أسلوب المشروع، المهم: لا
`await`
قبل الـ
`emit`
- راجع نفس النمط في أي مكان مشابه بعد نجاح دفع/تحديث طلب إذا كان يوقف الـ
UI

### تحسينات موصى بها في نفس الإصدار (مهمة)

1. في
`TrackingOrderFbDB.onCreateOrder`
أضف
timeout
داخلي أيضاً (دفاع إضافي):

```dart
await newOrderRef.set(orderData).timeout(const Duration(seconds: 3));
```

2. قبل إنشاء الطلب يوجد:

```dart
final analyticsCtx = await CsAnalytics.instance.collectOrderContext();
```

هذا يستدعي
Firebase Analytics
و
Branch
. أضف له
timeout
قصير (مثلاً 2 ثانية) حتى لا يؤخر بدء الطلب إذا علقت هذه الخدمات.  
إذا فشل/انتهى الوقت: أكمل إنشاء الطلب بدون
`analytics_context`
أو بسياق جزئي.

3. عند فشل الشبكة /
timeout
على
`client/orders`
بعد ما المستخدم ضغط إتمام:
   - لا تفترض تلقائياً أن الطلب ما انعمل
   - اعرض رسالة واضحة، واقترح فتح "طلباتي"
   - إذا وُجد طلب حديث مطابق خلال ثوانٍ: اعتبره نجاحاً بدل إنشاء طلب جديد
   - هذا يقلل التكرار عندما يقفل العميل التطبيق ويعيد المحاولة

---

## 4) ملفات يجب مراجعتها

| ملف | ماذا تراجع |
| --- | --- |
| `lib/screens/client/bill/bloc/create_order_bloc.dart` | ترتيب emit قبل Firebase |
| `lib/services/business/track_create_order/track_create_order_service.dart` | timeout على set/update |
| `lib/screens/client/bill/view/ui/bill_logic.dart` | `doneCreatedOrderFunc` / الانتقال لشاشة النجاح لمسار cash |
| `lib/services/business/analytics/clean_station_analytics.dart` | timeout على `collectOrderContext` |
| أي مسار مشابه لـ fast order / online payment tracking | لا يوقف UI على await خارجي |

---

## 5) شكل رد السيرفر لمسار ادفع عند التسليم

`POST /api/client/orders`

عند النجاح تقريباً:

```json
{
  "status": "success",
  "message": "...",
  "data": {
    "id": 12345
  }
}
```

- لا يوجد
`payment_url`
في مسار الدفع عند التسليم
- مسار البطاقة قد يحتوي
`payment_url`
- تأكد أن
`doneCreatedOrderFunc`
لمسار غير الأونلاين ينتقل مباشرة لـ
`SuccessCreateOrderScreen`
بدون انتظار رابط دفع

---

## 6) خطة اختبار قبل الرفع

### أساسي (يجب أن يمر)

1. طلب غسيل عادي + ادفع عند التسليم:
   - بعد النجاح ينتقل فوراً لشاشة النجاح
   - الـ
spinner
لا يبقى أكثر من زمن استجابة السيرفر فقط
2. محاكاة بطء/تعليق
Firebase
(قطع شبكة Google مؤقتاً / mock يعلّق):
   - الطلب ينجح على السيرفر
   - التطبيق رغم ذلك ينتقل لشاشة النجاح ولا يعلق
3. طلب أونلاين/بطاقة:
   - إذا رجع
`payment_url`
تفتح شاشة الدفع كالمعتاد
4. طلب سريع
fastorder
إن كان يستخدم نفس البلوك: نفس الحماية
5. فشل متعمد من السيرفر (422): يظهر خطأ ويتوقف الـ
spinner
(لا يبقى عالق)

### تراجع

- الكوبون
- المحفظة / النقاط
- إنشاء طلب مبيعات/زهور إن أمكن
- الرجوع من شاشة النجاح لطلباتي وظهور الطلب الجديد

---

## 7) معيار القبول

- لا يبقى زر إتمام الطلب على
spinner
بعد نجاح السيرفر لأي سبب جانبي (Firebase/Analytics/Branch)
- لا يحتاج العميل يقفل التطبيق عشان يشوف أن الطلب تم
- تقليل فرص الطلبات المكررة الناتجة عن إعادة المحاولة بعد تعليق UI

---

## 8) ما تم على السيرفر (للعلم — لا يغني عن إصلاح الموبايل)

تم في الـ backend إخراج تجهيز/إرسال تنبيه
Telegram
(بما فيه عدّ طلبات العميل) من مسار رد
`POST /api/client/orders`
إلى
Job
بعد
commit
حتى يرجع الرد للموبايل أسرع، خصوصاً تحت ضغط عملاء السعودية.

هذا يقلل احتمال بطء السيرفر، لكنه **لا يحل** السبنر اللانهائي بعد نجاح الرد.  
السبنر العالق + عدم فتح مودال الدفع سببه ترتيب الموبايل (انتظار Firebase قبل
`emit`
)، ويجب إصلاحه في نسخة التطبيق.

---

## 9) ملخص سطر واحد لمبرمج الموبايل

> بعد نجاح `client/orders` اعمل emit فوراً (نجاح كاش أو فتح مودال الأونلاين)، ولا تنتظر Firebase/Analytics؛ التتبع خلفية + timeout قصير.
