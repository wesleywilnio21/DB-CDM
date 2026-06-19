<?php

namespace App\Http\Controllers;

use App\Models\LetterAsset;
use App\Models\LetterTemplate;
use Illuminate\Http\Request;

class LetterTemplateController extends Controller
{
    public function index()
    {
        $templates = LetterTemplate::latest()->paginate(15);

        return view('letter_templates.index', compact('templates'));
    }

    public function create()
    {
        $logos = LetterAsset::logos()->latest()->get();
        $kops = LetterAsset::kops()->latest()->get();
        $ttds = LetterAsset::ttds()->latest()->get();

        return view('letter_templates.create', compact('logos', 'kops', 'ttds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'logo_asset_id' => 'nullable|exists:letter_assets,id',
            'kop_asset_id' => 'nullable|exists:letter_assets,id',
            'ttd_asset_id' => 'nullable|exists:letter_assets,id',
            'sig_text_above' => 'nullable|string|max:255',
            'sig_name' => 'nullable|string|max:255',
            'sig_position' => 'nullable|string|max:255',
        ]);

        LetterTemplate::create($validated);

        return redirect()->route('letter-templates.index')->with('success', 'Letter template created successfully.');
    }

    public function edit(LetterTemplate $letterTemplate)
    {
        $logos = LetterAsset::logos()->latest()->get();
        $kops = LetterAsset::kops()->latest()->get();
        $ttds = LetterAsset::ttds()->latest()->get();

        return view('letter_templates.edit', compact('letterTemplate', 'logos', 'kops', 'ttds'));
    }

    public function update(Request $request, LetterTemplate $letterTemplate)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'logo_asset_id' => 'nullable|exists:letter_assets,id',
            'kop_asset_id' => 'nullable|exists:letter_assets,id',
            'ttd_asset_id' => 'nullable|exists:letter_assets,id',
            'sig_text_above' => 'nullable|string|max:255',
            'sig_name' => 'nullable|string|max:255',
            'sig_position' => 'nullable|string|max:255',
        ]);

        $letterTemplate->update($validated);

        return redirect()->route('letter-templates.index')->with('success', 'Letter template updated successfully.');
    }

    public function destroy(LetterTemplate $letterTemplate)
    {
        $letterTemplate->delete();

        return redirect()->route('letter-templates.index')->with('success', 'Letter template deleted successfully.');
    }
}
