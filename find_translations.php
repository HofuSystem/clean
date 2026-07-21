<?php
$viewsDirs = [
    __DIR__ . '/packages/core/admin/src/resources/views',
    __DIR__ . '/packages/core/orders/src/resources/views',
];

$files = [];
foreach ($viewsDirs as $dir) {
    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
    }
}

$keys = [];
foreach ($files as $file) {
    $content = file_get_contents($file);
    preg_match_all("/(?:@lang|trans|__)\(['\"](.*?)['\"]\)/", $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $match) {
            $keys[$match] = true;
        }
    }
}

$arJsonFile = __DIR__ . '/lang/ar.json';
$enJsonFile = __DIR__ . '/lang/en.json';

$arJson = file_exists($arJsonFile) ? json_decode(file_get_contents($arJsonFile), true) ?: [] : [];
$enJson = file_exists($enJsonFile) ? json_decode(file_get_contents($enJsonFile), true) ?: [] : [];

$missingInAr = [];
$missingInEn = [];

// Pre-defined Arabic translations to ensure accuracy
$arabicMap = [
    "testing" => "اختبار",
    "testing orders" => "طلبات الاختبار",
    "technical date" => "تاريخ الفني",
    "technical time" => "وقت الفني",
    "select operator" => "اختر الموظف",
    "select representative" => "اختر المندوب",
    "search for order number" => "البحث عن رقم الطلب",
    "select pay_type" => "اختر طريقة الدفع",
    "search for client phone" => "البحث عن هاتف العميل",
    "payment orders" => "طلبات الدفع",
    "apply coupon" => "تطبيق الكوبون",
    "update delivery price" => "تحديث سعر التوصيل",
    "update cost" => "تحديث التكلفة",
    "B2B Financial Note" => "ملاحظة مالية B2B",
    "Electronic Invoice" => "فاتورة إلكترونية",
    "update wash type" => "تحديث نوع الغسيل",
    "new wash type" => "نوع الغسيل الجديد",
    "lab" => "المختبر",
    "washer" => "الغسالة",
    "select coupon" => "اختر الكوبون",
    "Order Operator" => "موظف الطلب",
    "Order Representative" => "مندوب الطلب",
    "Card" => "بطاقة",
    "Contract" => "عقد",
    "from status" => "من حالة",
    "to status" => "إلى حالة",
    "system" => "النظام",
    "#" => "#",
    "Type/Notes" => "النوع/الملاحظات",
    "Before" => "قبل",
    "After" => "بعد",
    "Changed By" => "بواسطة",
    "Changed At" => "تاريخ التغيير",
    "System" => "النظام",
    "Automatic system change" => "تغيير نظام تلقائي",
    "No history records found for this order." => "لم يتم العثور على سجلات تاريخ لهذا الطلب.",
    "Payment Transactions" => "معاملات الدفع",
    "Transaction ID" => "رقم المعاملة",
    "Amount (SAR)" => "المبلغ (ريال)",
    "Notes" => "ملاحظات",
    "Enter online payment method" => "أدخل طريقة الدفع عبر الإنترنت",
    "Enter notes" => "أدخل ملاحظات",
    "close" => "إغلاق",
    "Please wait" => "يرجى الانتظار",
    "Success" => "نجاح",
    "Transaction saved successfully" => "تم حفظ المعاملة بنجاح",
    "Error" => "خطأ",
    "An error occurred" => "حدث خطأ",
    "An error occurred while saving" => "حدث خطأ أثناء الحفظ",
    "Are you sure?" => "هل أنت متأكد؟",
    "You will not be able to recover this transaction!" => "لن تتمكن من استعادة هذه المعاملة!",
    "Yes, delete it!" => "نعم، احذفها!",
    "Deleting" => "جارٍ الحذف",
    "Deleted!" => "تم الحذف!",
    "Transaction has been deleted" => "تم حذف المعاملة",
    "Could not delete transaction" => "تعذر حذف المعاملة",
    "An error occurred while deleting" => "حدث خطأ أثناء الحذف",
    "Time" => "الوقت",
    "wash type" => "نوع الغسيل",
    "Customizations" => "التخصيصات",
    "final delete" => "حذف نهائي",
    "contract duration" => "مدة العقد",
    "duration count" => "عدد المدة",
    "days per week" => "أيام في الأسبوع",
    "visits details" => "تفاصيل الزيارات",
    "week: " => "أسبوع: ",
    "visit: " => "زيارة: ",
    "the order price is less than the coupon minimum" => "سعر الطلب أقل من الحد الأدنى للكوبون",
    "paid with wallet" => "مدفوع بالمحفظة",
    "paid with points" => "مدفوع بالنقاط",
    "paid with cash" => "مدفوع نقداً",
    "paid with card" => "مدفوع بالبطاقة",
    "has been refunded amount" => "تم استرداد المبلغ",
    "total paid" => "إجمالي المدفوع",
    "remaining customer" => "المتبقي للعميل",
    "no-description" => "بدون وصف",
    "company Details" => "تفاصيل الشركة",
    "admin reason" => "سبب الإدارة",
    "Order For" => "طلب لـ",
    "Recipient Name" => "اسم المستلم",
    "Recipient Phone" => "هاتف المستلم",
    "Request Address From Recipient" => "طلب العنوان من المستلم",
    "Hide Sender Identity" => "إخفاء هوية المرسل",
    "washer cost" => "تكلفة الغسيل",
    "lab cost" => "تكلفة المختبر",
    "total cost" => "التكلفة الإجمالية",
    "Discount Data" => "بيانات الخصم",
    "online Payment Method" => "طريقة الدفع عبر الإنترنت",
    "address description" => "وصف العنوان",
    "class" => "الفئة",
    "order report" => "تقرير الطلب",
    "Update B2B Financial Note" => "تحديث الملاحظة المالية B2B",
    "assign operators" => "تعيين الموظفين",
    "assign representative" => "تعيين مندوب",
    "assign operator" => "تعيين موظف",
    "Date from" => "من تاريخ",
    "search form date" => "البحث من تاريخ",
    "time from" => "من وقت",
    "search form time" => "البحث من وقت",
    "Date to" => "إلى تاريخ",
    "time to" => "إلى وقت",
];

$addedAr = 0;
$addedEn = 0;

foreach (array_keys($keys) as $key) {
    if (!array_key_exists($key, $arJson)) {
        if (isset($arabicMap[$key])) {
            $arJson[$key] = $arabicMap[$key];
        } else {
            // Default basic translation or just use key
            $arJson[$key] = $key;
        }
        $addedAr++;
    }
    if (!array_key_exists($key, $enJson)) {
        // Just title case the string for English
        $enJson[$key] = ucwords(str_replace(['_', '-'], ' ', $key));
        $addedEn++;
    }
}

if ($addedAr > 0) {
    file_put_contents($arJsonFile, json_encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
if ($addedEn > 0) {
    file_put_contents($enJsonFile, json_encode($enJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo "Added to AR: $addedAr\n";
echo "Added to EN: $addedEn\n";
