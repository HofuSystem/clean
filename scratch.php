<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    (new \Core\Users\Exports\UsersExport('ar', 10))->store('exports/test_error.csv', 'public');
    echo 'SUCCESS';
} catch (\Throwable $e) {
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
