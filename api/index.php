<?php
header('Content-Type: text/plain');

echo "Step 1: PHP works\n";

require __DIR__ . '/../vendor/autoload.php';
echo "Step 2: Autoload loaded\n";

$app = require_once __DIR__ . '/../bootstrap/app.php';
echo "Step 3: App bootstrapped\n";

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
echo "Step 4: Kernel created\n";

flush();

$request = \Illuminate\Http\Request::capture();
echo "Step 5: Request captured\n";
flush();

$response = $kernel->handle($request);
echo "Step 6: Response handled\n";
flush();

$response = $kernel->handle($request);
echo "Step 6: Response handled\n";
flush();

echo "Response status: " . $response->getStatusCode() . "\n";
echo "----- RESPONSE CONTENT -----\n";
echo $response->getContent();

exit;
