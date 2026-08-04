<?php
declare(strict_types=1);

if ($_GET['debug_routes'] ?? false) {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $routes = app('router')->getRoutes();
    header('Content-Type: text/plain');
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'film-likes')) {
            echo implode('|', $route->methods()) . ' /' . $route->uri() . "\n";
        }
    }
    exit;
}

require __DIR__ . '/../public/index.php';
