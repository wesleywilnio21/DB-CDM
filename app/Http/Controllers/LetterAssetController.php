<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreLetterAssetRequest;
use App\Models\LetterAsset;
use App\Services\LetterAssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LetterAssetController extends Controller
{
    public function __construct(
        private readonly LetterAssetService $letterAssetService
    ) {
        $this->authorizeSuperAdmin();
    }

    public function index(Request $request): View
    {
        $query = LetterAsset::query()->latest();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $assets = $query->paginate(20);

        return view('letter_assets.index', compact('assets'));
    }

    public function store(StoreLetterAssetRequest $request): RedirectResponse
    {
        $this->letterAssetService->store($request->validated(), $request->file('file'));

        return back()->with('success', 'Asset uploaded successfully.');
    }

    public function destroy(LetterAsset $letterAsset): RedirectResponse
    {
        if ($letterAsset->file_path && Storage::disk('local')->exists($letterAsset->file_path)) {
            Storage::disk('local')->delete($letterAsset->file_path);
        }

        $letterAsset->delete();

        return back()->with('success', 'Asset deleted successfully.');
    }
}
