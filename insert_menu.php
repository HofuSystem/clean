<?php
$jsonFile = __DIR__ . '/packages/core/admin/src/nav/admin-nav.json';
if (!file_exists($jsonFile)) {
    die("File not found");
}

$data = json_decode(file_get_contents($jsonFile), true);

$newMenuItem = [
    "url" => null,
    "route" => "dashboard.image-uploader.index",
    "permission" => null,
    "icon" => "fa fa-upload",
    "type" => "li",
    "title" => [
        "en" => "Image Uploader",
        "ar" => "خدمة رفع الصور"
    ]
];

// Find "System Settings" title and insert right after it. Or just find "General Settings".
$inserted = false;
foreach ($data as $index => $item) {
    if (isset($item['route']) && $item['route'] == 'dashboard.settings.settings') {
        array_splice($data, $index + 1, 0, [$newMenuItem]);
        $inserted = true;
        break;
    }
}

if (!$inserted) {
    // If not found, just append to the end
    $data[] = $newMenuItem;
}

file_put_contents($jsonFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "Menu item inserted successfully.\n";
