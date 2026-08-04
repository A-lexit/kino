<?php

use Illuminate\Http\Request;


require __DIR__ . '/../vendor/autoload.php';

// Створюємо структуру тимчасових папок у /tmp
$tmpStorage = '/tmp/storage';
$tmpCache = '/tmp/bootstrap/cache';

$dirs = [
    $tmpStorage . '/logs',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/app',
    $tmpCache,
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Задаємо системні змінні для кешу та клейких файлів
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_ENV['APP_SERVICES_CACHE'] = $tmpCache . '/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $tmpCache . '/packages.php';
$_ENV['APP_CONFIG_CACHE']   = $tmpCache . '/config.php';
$_ENV['APP_ROUTES_CACHE']   = $tmpCache . '/routes.php';
$_ENV['APP_EVENTS_CACHE']   = $tmpCache . '/events.php';

// Завантажуємо додаток
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Перенаправляємо основний storage в /tmp
$app->useStoragePath($tmpStorage);

// Обробляємо запит
$app->handleRequest(Request::capture());
