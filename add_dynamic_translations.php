<?php
$dynamicKeys = [
    'clothes' => 'ملابس',
    'sales' => 'مبيعات',
    'services' => 'خدمات',
    'host' => 'ضيافة',
    'maid' => 'عاملة منزلية',
    'care' => 'رعاية',
    'selfcare' => 'عناية شخصية',
    'maidflex' => 'عاملة مرنة',
    'fastorder' => 'طلب سريع',
    'canceled' => 'ملغي',
    'rejected' => 'مرفوض',
    'issue' => 'مشكلة',
    'pending' => 'طلب جديد',
    'ready_to_delivered' => 'جاهز للتسليم',
    'in_the_way' => 'في الطريق',
    'receiving_driver_accepted' => 'تم قبول سائق الاستلام',
    'delivered' => 'تم التوصيل',
    'finished' => 'منتهي',
    'order_has_been_delivered_to_admin' => 'تم استلام الطلب من الإدارة',
    'technical_accepted' => 'تم قبول الفني',
    'service_started' => 'بدأت الخدمة',
    'cash' => 'كاش',
    'card' => 'بطاقة',
    'wallet' => 'محفظة',
    'Bronze' => 'برونزي',
    'Silver' => 'فضي',
    'Gold' => 'ذهبي',
    'Platinum' => 'بلاتيني',
    'received' => 'مستلم'
];

$arJsonFile = __DIR__ . '/lang/ar.json';
$enJsonFile = __DIR__ . '/lang/en.json';

$arJson = file_exists($arJsonFile) ? json_decode(file_get_contents($arJsonFile), true) ?: [] : [];
$enJson = file_exists($enJsonFile) ? json_decode(file_get_contents($enJsonFile), true) ?: [] : [];

$addedAr = 0;
$addedEn = 0;

foreach ($dynamicKeys as $key => $arValue) {
    if (!array_key_exists($key, $arJson)) {
        $arJson[$key] = $arValue;
        $addedAr++;
    }
    if (!array_key_exists($key, $enJson)) {
        $enJson[$key] = ucwords(str_replace('_', ' ', $key));
        $addedEn++;
    }
}

if ($addedAr > 0) {
    file_put_contents($arJsonFile, json_encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
if ($addedEn > 0) {
    file_put_contents($enJsonFile, json_encode($enJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo "Dynamic keys added to AR: $addedAr\n";
echo "Dynamic keys added to EN: $addedEn\n";
