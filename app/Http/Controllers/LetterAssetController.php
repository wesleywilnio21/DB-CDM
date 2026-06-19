<?php

namespace App\Http\Controllers;

use App\Models\LetterAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class LetterAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = LetterAsset::query()->latest();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $assets = $query->paginate(20);

        return view('letter_assets.index', compact('assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:logo,kop,ttd',
            'name' => 'required|string|max:255',
            'file' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $path = $request->file('file')->store('letter_assets');

        // Resize image if it's too large to prevent out of memory errors
        try {
            $absolutePath = storage_path('app/private/'.$path);
            $manager = new ImageManager(new Driver);
            $image = $manager->decodePath($absolutePath);

            if ($image->width() > 800) {
                // Resize to max 800px width, keeping aspect ratio
                $image->scale(width: 800);
                $image->save($absolutePath);
            }
        } catch (\Exception $e) {
            // If resizing fails, continue with original file
            Log::error('Image resize failed: '.$e->getMessage());
        }

        LetterAsset::create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'file_path' => $path,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Asset uploaded successfully.');
    }

    public function destroy(LetterAsset $letterAsset)
    {
        if ($letterAsset->file_path && Storage::disk('local')->exists($letterAsset->file_path)) {
            Storage::disk('local')->delete($letterAsset->file_path);
        }

        $letterAsset->delete();

        return back()->with('success', 'Asset deleted successfully.');
    }
}
