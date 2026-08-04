<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');

    echo "MESSAGE:\n";
    echo $e->getMessage()."\n\n";

    echo "FILE:\n";
    echo $e->getFile().':'.$e->getLine()."\n\n";

    echo "TRACE:\n";
    echo $e->getTraceAsString();
}
