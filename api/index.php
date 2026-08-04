<?php
header('Content-Type: text/plain');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

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
