<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$assets = \App\Models\LetterAsset::all();
foreach($assets as $a) {
    $path = storage_path('app/private/' . $a->file_path);
    echo $a->id . ' - expected path: ' . $path . ' - exists: ' . (file_exists($path) ? 'YES' : 'NO') . "\n";
}
