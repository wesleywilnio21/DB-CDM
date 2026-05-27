<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        Tag::create($validated);

        return back()->with('success', 'Tag created successfully.');
    }

    public function destroy(Tag $tag)
    {
        $this->authorizeSuperAdmin();
        $tag->delete();
        return back()->with('success', 'Tag deleted successfully.');
    }

    protected function authorizeSuperAdmin()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
