<?php

echo 'APP_DEBUG = ';
var_dump(getenv('APP_DEBUG'));

echo '<br>';

echo 'APP_KEY = ';
var_dump(getenv('APP_KEY'));

echo '<br>';

echo 'APP_ENV = ';
var_dump(getenv('APP_ENV'));

exit;
