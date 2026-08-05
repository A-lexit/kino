<?php

// 1. Примусово вмикаємо вивід усіх помилок PHP на найвищому рівні
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// 2. Огортаємо АБСОЛЮТНО ВСЕ (включно з bootstrap) у try-catch
try {
    // Примусово вмикаємо режим дебагу Laravel
    putenv('APP_DEBUG=true');
    $_ENV['APP_DEBUG'] = 'true';
    $_SERVER['APP_DEBUG'] = 'true';

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

    putenv("VIEW_COMPILED_PATH={$tmpStorage}/framework/views");
    putenv("APP_PACKAGES_CACHE={$tmpBootstrapCache}/packages.php");
    putenv("APP_SERVICES_CACHE={$tmpBootstrapCache}/services.php");

    $_ENV['VIEW_COMPILED_PATH'] = $_SERVER['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
    $_ENV['APP_PACKAGES_CACHE'] = $_SERVER['APP_PACKAGES_CACHE'] = $tmpBootstrapCache . '/packages.php';
    $_ENV['APP_SERVICES_CACHE'] = $_SERVER['APP_SERVICES_CACHE'] = $tmpBootstrapCache . '/services.php';

    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath($tmpStorage);

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';

    $request = \Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);

    // Якщо Laravel опрацював помилку внутрішньо і повернув 500 — витягуємо Exception вручну
    if ($response->getStatusCode() === 500 && isset($response->exception)) {
        throw $response->exception;
    }

    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    // Вивід реального стек-трейсу прямо в браузер
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/html; charset=utf-8');
    
    echo "<div style='font-family: monospace; padding: 20px; background: #fff0f0; border: 2px solid #ff0000;'>";
    echo "<h1 style='color: #c00000; margin-top: 0;'>Vercel / Laravel Execution Error</h1>";
    echo "<p><b>Message:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>File:</b> " . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background: #fff; padding: 10px; border: 1px solid #ccc; overflow: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
    exit;
}
