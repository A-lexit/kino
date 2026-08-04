<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/../vendor/autoload.php';

echo "autoload OK<br>";

$app = require __DIR__.'/../bootstrap/app.php';

echo "bootstrap OK<br>";

var_dump(get_class($app));

echo "<br>";

echo "Has config? ";
var_dump($app->bound('config'));

echo "<br>";

echo "Has events? ";
var_dump($app->bound('events'));

echo "<br>";

echo "Has files? ";
var_dump($app->bound('files'));

echo "<br>";

echo "Has log? ";
var_dump($app->bound('log'));

exit;
