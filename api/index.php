<?php
header('Content-Type: text/plain');

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

$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_ENV['APP_PACKAGES_CACHE'] = $tmpBootstrapCache . '/packages.php';
$_ENV['APP_SERVICES_CACHE'] = $tmpBootstrapCache . '/services.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->useStoragePath($tmpStorage);

$app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, function ($app) {
    return new class($app) extends \Illuminate\Foundation\Exceptions\Handler {
        public function render($request, \Throwable $e)
        {
            $content = "CAUGHT: " . get_class($e) . "\n" .
                "MESSAGE: " . $e->getMessage() . "\n" .
                "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n\n" .
                "TRACE:\n" . $e->getTraceAsString();

            return new \Symfony\Component\HttpFoundation\Response(
                $content,
                500,
                ['Content-Type' => 'text/plain']
            );
        }
    };
});

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
if (($_GET['debug_routes'] ?? false)) {
    $kernel->bootstrap();
    header('Content-Type: text/plain');
    foreach (app('router')->getRoutes() as $route) {
        echo implode('|', $route->methods()) . ' ' . $route->uri() . "\n";
    }
    exit;
}
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
