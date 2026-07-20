<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiService = app(App\Services\ExternalApiService::class);
$rest = $apiService->getRestCountriesData('AG');
print_r($rest);
