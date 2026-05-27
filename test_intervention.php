<?php
require __DIR__ . '/vendor/autoload.php';
$manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
$path = __DIR__ . '/storage/app/private/letter_assets/AxrS5dwrfSMvjDb6GukgJuM40fNdUGu5FdxqLySW.png';
$image = $manager->decodePath($path);
echo "Width: " . $image->width() . "\n";
$image->scale(width: 800);
echo "New width: " . $image->width() . "\n";
$image->save($path);
echo "Saved.\n";
