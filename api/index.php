<?php
use Illuminate\Http\Request;

require __DIR__ . '/../vendor/autoload.php';

$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage . '/logs',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/app',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath($tmpStorage);

$app->handleRequest(Request::capture());
