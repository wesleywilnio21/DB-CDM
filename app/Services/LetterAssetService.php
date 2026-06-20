<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LetterAsset;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LetterAssetService
{
    /**
     * Simpan file aset surat: upload, resize jika perlu, lalu buat record di DB.
     */
    public function store(array $data, UploadedFile $file): LetterAsset
    {
        $path = $file->store('letter_assets');

        $this->resizeIfNeeded(storage_path('app/private/' . $path));

        return LetterAsset::create([
            'type'       => $data['type'],
            'name'       => $data['name'],
            'file_path'  => $path,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Resize gambar ke maksimum 800px lebar jika melebihi batas.
     */
    private function resizeIfNeeded(string $absolutePath): void
    {
        try {
            $manager = new ImageManager(new Driver);
            $image   = $manager->decodePath($absolutePath);

            if ($image->width() > 800) {
                $image->scale(width: 800);
                $image->save($absolutePath);
            }
        } catch (\Exception $e) {
            Log::error('Image resize failed: ' . $e->getMessage());
        }
    }
}
