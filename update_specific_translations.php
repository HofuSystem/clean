<?php
$arJsonFile = __DIR__ . '/lang/ar.json';
$enJsonFile = __DIR__ . '/lang/en.json';

$arJson = file_exists($arJsonFile) ? json_decode(file_get_contents($arJsonFile), true) ?: [] : [];
$enJson = file_exists($enJsonFile) ? json_decode(file_get_contents($enJsonFile), true) ?: [] : [];

// List of specific keys we want to override in Arabic
$arOverrides = [
    'col.received' => 'مستلم',
    'col.canceled' => 'ملغي',
    'col.delivered' => 'تم التوصيل',
    'col.payment' => 'دفع',
    'Write a comment...' => 'اكتب تعليقاً...',
    'select_switch' => 'تحديد الخيار'
];

foreach ($arOverrides as $key => $arValue) {
    $arJson[$key] = $arValue;
    // Also ensure they are in English correctly
    if (!array_key_exists($key, $enJson)) {
        if ($key === 'select_switch') {
            $enJson[$key] = 'Select Switch';
        } else {
            $enJson[$key] = str_replace('col.', '', $key);
        }
    } else {
        // Fix up the English ones if they were just placeholders
        if ($key === 'select_switch') $enJson[$key] = 'Select Switch';
        if ($key === 'col.received') $enJson[$key] = 'Received';
        if ($key === 'col.canceled') $enJson[$key] = 'Canceled';
        if ($key === 'col.delivered') $enJson[$key] = 'Delivered';
        if ($key === 'col.payment') $enJson[$key] = 'Payment';
    }
}

file_put_contents($arJsonFile, json_encode($arJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents($enJsonFile, json_encode($enJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Updated keys successfully.\n";
