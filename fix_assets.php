<?php

use App\Models\LetterAsset;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$assets = LetterAsset::all();
foreach ($assets as $a) {
    if (str_starts_with($a->file_path, 'private/letter_assets/')) {
        $oldPath = storage_path('app/private/'.$a->file_path);
        $newPathSuffix = str_replace('private/letter_assets/', 'letter_assets/', $a->file_path);
        $newPath = storage_path('app/private/'.$newPathSuffix);

        if (! file_exists(dirname($newPath))) {
            mkdir(dirname($newPath), 0755, true);
        }

        if (file_exists($oldPath)) {
            rename($oldPath, $newPath);
            echo "Moved $oldPath to $newPath\n";
        }

        $a->file_path = $newPathSuffix;
        $a->save();
        echo "Updated DB ID {$a->id} to $newPathSuffix\n";
    }
}
