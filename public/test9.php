<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $response = Illuminate\Support\Facades\Http::withHeaders([
        'User-Agent' => 'SCM-Monitoring-App/1.0'
    ])->withoutVerifying()->timeout(5)->get("https://nominatim.openstreetmap.org/search", [
        'countrycodes' => 'AG',
        'format' => 'json',
        'limit' => 1
    ]);
    if ($response->successful()) {
        print_r($response->json());
    } else {
        echo "HTTP Error: " . $response->status() . " - " . $response->body();
    }
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage();
}
