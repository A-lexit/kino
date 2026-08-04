<?php
declare(strict_types=1);

$tmpBase = '/tmp/storage';
foreach (['framework/views', 'framework/cache/data', 'framework/sessions', 'logs'] as $dir) {
    $path = $tmpBase . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo $e->getTraceAsString();
}
