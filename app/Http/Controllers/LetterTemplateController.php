<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreLetterTemplateRequest;
use App\Http\Requests\UpdateLetterTemplateRequest;
use App\Models\LetterAsset;
use App\Models\LetterTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LetterTemplateController extends Controller
{
    public function index(): View
    {
        $templates = LetterTemplate::latest()->paginate(15);

        return view('letter_templates.index', compact('templates'));
    }

    public function create(): View
    {
        $logos = LetterAsset::logos()->latest()->get();
        $kops  = LetterAsset::kops()->latest()->get();
        $ttds  = LetterAsset::ttds()->latest()->get();

        return view('letter_templates.create', compact('logos', 'kops', 'ttds'));
    }

    public function store(StoreLetterTemplateRequest $request): RedirectResponse
    {
        LetterTemplate::create($request->validated());

        return redirect()->route('letter-templates.index')->with('success', 'Letter template created successfully.');
    }

    public function edit(LetterTemplate $letterTemplate): View
    {
        $logos = LetterAsset::logos()->latest()->get();
        $kops  = LetterAsset::kops()->latest()->get();
        $ttds  = LetterAsset::ttds()->latest()->get();

        return view('letter_templates.edit', compact('letterTemplate', 'logos', 'kops', 'ttds'));
    }

    public function update(UpdateLetterTemplateRequest $request, LetterTemplate $letterTemplate): RedirectResponse
    {
        $letterTemplate->update($request->validated());

        return redirect()->route('letter-templates.index')->with('success', 'Letter template updated successfully.');
    }

    public function destroy(LetterTemplate $letterTemplate): RedirectResponse
    {
        $letterTemplate->delete();

        return redirect()->route('letter-templates.index')->with('success', 'Letter template deleted successfully.');
    }
}
