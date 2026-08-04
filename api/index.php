<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';

echo "autoload ok<br>";

$app = require __DIR__.'/../bootstrap/app.php';

echo "bootstrap ok<br>";

var_dump(get_class($app));

exit;
