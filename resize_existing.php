<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$assets = \App\Models\LetterAsset::all();
$manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

foreach($assets as $a) {
    $path = storage_path('app/private/' . $a->file_path);
    if (file_exists($path)) {
        try {
            $image = $manager->decodePath($path);
            if ($image->width() > 800) {
                echo "Resizing {$a->id} (original width: {$image->width()})\n";
                $image->scale(width: 800);
                $image->save($path);
            }
        } catch (\Exception $e) {
            echo "Error resizing {$a->id}: " . $e->getMessage() . "\n";
        }
    }
}
echo "Done.\n";
