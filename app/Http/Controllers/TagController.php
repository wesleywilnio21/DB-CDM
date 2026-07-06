<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;

class TagController extends Controller
{
    public function store(StoreTagRequest $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        Tag::create($request->validated());

        return back()->with('success', 'Tag created successfully.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $this->authorizeSuperAdmin();
        $tag->delete();

        return back()->with('success', 'Tag deleted successfully.');
    }
}
