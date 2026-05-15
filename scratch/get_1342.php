<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
use Illuminate\Support\Facades\DB;

try {
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $k = DB::table('kardexes')->where('id', 1342)->first();
    print_r($k);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
