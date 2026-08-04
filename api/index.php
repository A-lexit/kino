<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

// 1. Vercel дозволяє писати ТІЛЬКИ в /tmp. Створюємо там структуру для Laravel.
$tmpStorage = '/tmp/storage';
$dirs = [
    $tmpStorage . '/logs',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/app'
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// 2. Форсуємо використання безпечних драйверів для Serverless
$_ENV['LOG_CHANNEL'] = 'stderr'; // Логи йтимуть в консоль Vercel, а не в файл
$_ENV['SESSION_DRIVER'] = 'cookie'; // Файлові сесії не працюють в Serverless
$_ENV['CACHE_STORE'] = 'array'; // Файловий кеш не зберігається між запитами

$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Вказуємо Laravel використовувати /tmp замість стандартної папки storage
$app->useStoragePath($tmpStorage);

// 4. Запускаємо ядро
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
