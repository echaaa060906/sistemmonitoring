<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$c = Illuminate\Support\Facades\DB::table('countries')->where('name', 'Antigua and Barbuda')->first();
print_r($c);
