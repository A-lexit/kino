<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

echo "Application OK<br>";

try {
    $config = $app->make('config');

    echo "Config OK<br>";

    var_dump(get_class($config));
} catch (Throwable $e) {
    echo "<pre>";
    echo $e;
    echo "</pre>";
}

exit;
