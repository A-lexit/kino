<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Перевірка на режим обслуговування (Maintenance mode)
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Завантаження автолоадера Composer
require __DIR__.'/../vendor/autoload.php';

// Запуск додатку Laravel (новий синтаксис для Laravel 11+)
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());
