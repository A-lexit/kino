<?php
header('Content-Type: text/plain');

echo "Step 1: PHP works\n";

require __DIR__ . '/../vendor/autoload.php';
echo "Step 2: Autoload loaded\n";

$app = require_once __DIR__ . '/../bootstrap/app.php';
echo "Step 3: App bootstrapped\n";

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
echo "Step 4: Kernel created\n";

exit;
