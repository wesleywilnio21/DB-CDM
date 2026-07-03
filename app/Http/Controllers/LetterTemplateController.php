<?php

namespace App\Http\Controllers;

use App\Models\LetterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LetterTemplateController extends Controller
{
    public function index()
    {
        $templates = LetterTemplate::latest()->paginate(10);
        return view('letter_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('letter_templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'number_format' => 'required|string|max:255',
            'signatory_name' => 'nullable|string|max:255',
            'signatory_position' => 'nullable|string|max:255',
            'signature_image' => 'nullable|image|max:2048',
            'stamp_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('signature_image')) {
            $validated['signature_image'] = $request->file('signature_image')->store('signatures', 'public');
        }

        if ($request->hasFile('stamp_image')) {
            $validated['stamp_image'] = $request->file('stamp_image')->store('stamps', 'public');
        }

        LetterTemplate::create($validated);

        return redirect()->route('letter-templates.index')->with('success', 'Template created successfully.');
    }

    public function show(LetterTemplate $letterTemplate)
    {
        return view('letter_templates.show', compact('letterTemplate'));
    }

    public function edit(LetterTemplate $letterTemplate)
    {
        return view('letter_templates.edit', compact('letterTemplate'));
    }

    public function update(Request $request, LetterTemplate $letterTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'nullable|string',
            'number_format' => 'required|string|max:255',
            'signatory_name' => 'nullable|string|max:255',
            'signatory_position' => 'nullable|string|max:255',
            'signature_image' => 'nullable|image|max:2048',
            'stamp_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('signature_image')) {
            if ($letterTemplate->signature_image) {
                Storage::disk('public')->delete($letterTemplate->signature_image);
            }
            $validated['signature_image'] = $request->file('signature_image')->store('signatures', 'public');
        }

        if ($request->hasFile('stamp_image')) {
            if ($letterTemplate->stamp_image) {
                Storage::disk('public')->delete($letterTemplate->stamp_image);
            }
            $validated['stamp_image'] = $request->file('stamp_image')->store('stamps', 'public');
        }

        $letterTemplate->update($validated);

        return redirect()->route('letter-templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(LetterTemplate $letterTemplate)
    {
        $letterTemplate->delete();
        return redirect()->route('letter-templates.index')->with('success', 'Template deleted successfully.');
    }

    public function getVariables(LetterTemplate $letterTemplate)
    {
        // Temukan semua kata yang ada di dalam {{ }} menggunakan regex
        preg_match_all('/\{\{([\w_]+)\}\}/', $letterTemplate->content, $matches);

        $variables = [];
        if (!empty($matches[1])) {
            // Unik dan jangan masukkan variabel standar form
            $variables = array_values(array_unique($matches[1]));
            $ignored = ['nama_umat', 'alamat', 'telepon', 'nomor_surat', 'tanggal_surat'];
            $variables = array_diff($variables, $ignored);
        }

        return response()->json(array_values($variables));
    }
}
