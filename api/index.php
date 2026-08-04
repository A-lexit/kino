<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {

    use Illuminate\Http\Request;

    define('LARAVEL_START', microtime(true));

    if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    require __DIR__.'/../vendor/autoload.php';

    $app = require_once __DIR__.'/../bootstrap/app.php';

    $app->handleRequest(Request::capture());

} catch (Throwable $e) {

    echo "<pre>";

    echo $e->getMessage() . PHP_EOL . PHP_EOL;

    echo $e->getFile() . ":" . $e->getLine() . PHP_EOL . PHP_EOL;

    echo $e->getTraceAsString();

    echo "</pre>";
}
