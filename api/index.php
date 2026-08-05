<?php

require __DIR__ . '/../vendor/autoload.php';

$tmpStorage = '/tmp/storage';
$tmpBootstrapCache = '/tmp/bootstrap/cache';

$dirs = [
    $tmpStorage . '/logs',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/app',
    $tmpBootstrapCache,
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Laravel env() читає дані через putenv() та $_SERVER
putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
putenv("APP_PACKAGES_CACHE={$tmpBootstrapCache}/packages.php");
putenv("APP_SERVICES_CACHE={$tmpBootstrapCache}/services.php");

$_ENV['VIEW_COMPILED_PATH'] = $_SERVER['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_ENV['APP_PACKAGES_CACHE'] = $_SERVER['APP_PACKAGES_CACHE'] = $tmpBootstrapCache . '/packages.php';
$_ENV['APP_SERVICES_CACHE'] = $_SERVER['APP_SERVICES_CACHE'] = $tmpBootstrapCache . '/services.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath($tmpStorage);

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

if ($_GET['debug_routes'] ?? false) {
    $kernel->bootstrap();
    header('Content-Type: text/plain');
    foreach (app('router')->getRoutes() as $route) {
        echo implode('|', $route->methods()) . ' ' . $route->uri() . "\n";
    }
    exit;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

try {
    $request = \Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h1>500 Internal Server Error</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
