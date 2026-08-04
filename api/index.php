<?php
header('Content-Type: text/plain');

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

$app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, function ($app) {
    return new class($app) extends \Illuminate\Foundation\Exceptions\Handler {
        public function render($request, \Throwable $e)
        {
            return response(
                "CAUGHT: " . get_class($e) . "\n" .
                "MESSAGE: " . $e->getMessage() . "\n" .
                "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n\n" .
                "TRACE:\n" . $e->getTraceAsString(),
                500,
                ['Content-Type' => 'text/plain']
            );
        }
    };
});

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
