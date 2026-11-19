# تحسين سريع إضافي: استرجاع الطلب عند timeout بعد إتمام الطلب

تاريخ: 2026-11-19 (محدّث بعد فحص السيرفر)  
الأولوية: متوسطة (بعد إصلاح السبنر الأساسي)  
المشروع: `clean-station-flutter`  
الهدف: تقليل الطلبات المكررة إذا السيرفر أنشأ الطلب والرد ما وصل للتطبيق

---

## نتائج فحص السيرفر (مهم قبل التنفيذ)

### 1) ترتيب القائمة: صحيح
`GET client/orders` يستخدم:

```php
->latest()->paginate(10);
```

يعني الأحدث أولاً. تقدر تاخذ أول عنصر من الصفحة 1 كأحدث طلب.

### 2) `created_at`: مش ISO وفيه تاريخ فقط
الـ API بيرجع:

```text
"created_at": "19-11-2026"
```

الصيغة: `d-m-Y` (يوم-شهر-سنة) **بدون وقت**.

- `DateTime.parse` في Dart على ISO مش مناسب لهذه الصيغة
- حتى لو اتقرت، ما فيثوانٍ/دقائق عشان نافذة 60–120 ثانية

نفس الصيغة في:

- قائمة الطلبات (`OrderResource` / `OrderWithOutItemsResource`)
- تفاصيل الطلب (`OrderDetailsResource` / `OrderDetailsWithOutItemsResource`)
- بما فيها مسار الزهور/المبيعات

**خلاصة:** لا تعتمد على `created_at` لنافذة زمنية بالثواني.

بديل عملي للاسترجاع:

- خذ أول طلب من القائمة (الأحدث)
- طابق `type` المناسب
- وطابق تقريباً `total_price` / عنوان إن توفر
- أو اعتبر أحدث طلب خلال نفس الجلسة بعد فشل النقل مباشرة (بدون parse للوقت)

### 3) تبويب الأنواع: جزئياً صحيح

من السيرفر:

| `type` المرسل من التطبيق | ماذا يرجع السيرفر |
| --- | --- |
| `clothes` | `clothes` + `fastorder` معاً |
| `services` | فقط `type = services` حرفياً |
| `sales` | فقط `sales` (زهور/مبيعات) |
| أي قيمة أخرى مثل `maidflex` | فلتر exact على نفس القيمة |

يعني:

- الطلب السريع `fastorder` **يظهر** تحت استدعاء `type=clothes` — صحيح
- العاملات (`maidflex` / `maidscheduled` / `maidPackage` / `maidoffer`) **لا** ترجع تلقائياً تحت `type=services`
- لاسترجاع طلب عاملة لازم تنادي بنفس الـ `type` الحقيقي للطلب (مثل `maidflex`)

لو ناديت `services` بالغلط لاسترجاع `maidflex`، مش هتلاقي الطلب، وهترجع لنفس سلوك الخطأ الحالي بدون ضرر إضافي.

### 4) طلب البطاقة `pending_payment`: مشكلة كبيرة على الاسترجاع الحالي

قائمة الطلبات وتفاصيل الطلب **تستبعدان** هذه الحالات:

```php
->whereNotIn('status', ['pending_payment', 'failed_payment', 'cancel_payment'])
```

يعني طلب أونلاين لسه `pending_payment`:

- مش هيظهر في `GET client/orders`
- ومش هيتفتح بـ `GET client/orders/{id}`
- ومورد التفاصيل **ما بيرجع** `payment_url`

وفي تفاصيل الطلب الحالية:

- في زر/مسار "ادفع الآن" لمسار الدفع اللاحق (خصوصاً الطلب السريع عبر `pay_fastorder`)
- هذا **مش** استكمال لنفس `pending_payment` الناتج من إنشاء الطلب الأونلاين اللي الرد فيه انقطع

**خلاصة:** استرجاع قائمة الطلبات يفيد أساساً مسار **ادفع عند التسليم / الطلب الظاهر بعد الإنشاء**.  
مسار البطاقة اللي اتعمل `pending_payment` وما رجعش `payment_url` للتطبيق: **الاسترجاع من طلباتي مش هيكفي** بدون دعم إضافي من السيرفر أو سياسة منتج واضحة (مثلاً إلغاء الصامت وإعادة إنشاء، أو endpoint يجيب pending payment orders + payment url).

---

## المطلوب تنفيذه بسرعة (بعد التصحيح أعلاه)

في `CreateOrderBloc._fCreateOrder` عند فشل الشبكة / timeout فقط (مش أي 422):

### أ) كاش / ادفع عند التسليم / طلب ظاهر في القائمة

1. نادُه:
   - غسيل/سريع: `GET client/orders?type=clothes&page=1`
   - زهور: `GET client/orders?type=sales&page=1`
   - خدمات حرفية `services`: `GET client/orders?type=services&page=1`
   - عاملات: `GET client/orders?type=<نفس type الطلب>&page=1` مثل `maidflex`
2. خذ أول عنصر (الأحدث)
3. إذا النوع مطابق (أو `clothes`/`fastorder` ضمن تبويب الغسيل) والسعر قريب من اللي اتبعت:
   - `emit(DoneCreateOrderState)` مع `orderId`
   - روح شاشة النجاح
4. غير هيك: فشل عادي

لا تعتمد على parse لـ `created_at`.

### ب) أونلاين / بطاقة (`pending_payment`)

مع الـ API الحالي:

- لا تعتمد أنك هتلاقي الطلب في طلباتي
- أفضل سلوك آمن حالياً:
  - رسالة واضحة: يحتمل الطلب اتعمل؛ راجع الدعم/لا تعيد فوراً إن ظهر خصم مكرر
  - أو اتركها فشل شبكة عادي كما هي
- لا تفتح مودال دفع فاضي بدون `payment_url`

أي استرجاع كامل للأونلاين يحتاج تعديل سيرفر منفصل (إظهار `pending_payment` للعميل + إرجاع/`recreate` لـ payment_url). هذا خارج التنفيذ السريع للموبايل وحده.

---

## مثال اتجاه مقبول (للكاش فقط)

```dart
if (response.success) {
  // النجاح الحالي كما هو
  return;
}

if (_looksLikeTransportFailure(response)) {
  final recoveredId = await _tryRecoverVisibleOrder(event.inputData);
  if (recoveredId != null) {
    emit(DoneCreateOrderState(
      msg: response.msg,
      response: '${response.toMap()}',
      orderId: recoveredId,
      paymentUrl: null,
    ));
    return;
  }
}

emit(FailedCreateOrderState(
  msg: response.msg,
  errType: response.errType ?? 0,
  response: '${response.toMap()}',
));
```

`_tryRecoverVisibleOrder`:

- يختار `type` الصحيح حسب نوع الفاتورة
- يجيب page=1
- يطابق أول طلب مناسب
- يرجع `id` أو null

---

## اختبار سريع

1. كاش غسيل + timeout بعد إنشاء حقيقي: يسترجع من `type=clothes` ويعدّها نجاح.
2. سريع `fastorder` يظهر تحت `clothes`.
3. `maidflex` يسترجع بـ `type=maidflex` مش `services`.
4. أونلاين `pending_payment`: القائمة فارغة منه؛ لا كسر ولا مودال فاضي.
5. 422 حقيقي: بدون استرجاع خاطئ.

---

## ملخص سطر واحد

> استرجاع الطلبات الظاهرة (كاش) من أول عنصر في `client/orders` حسب النوع الصحيح، بدون الاعتماد على `created_at`. طلبات البطاقة `pending_payment` مش ظاهرة في API الحالي فلا تبني الاسترجاع عليها وحدها.
