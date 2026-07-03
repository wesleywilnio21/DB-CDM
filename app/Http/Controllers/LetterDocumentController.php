<?php

namespace App\Http\Controllers;

use App\Models\LetterDocument;
use Illuminate\Http\Request;

class LetterDocumentController extends Controller
{
    public function index()
    {
        $documents = LetterDocument::with(['letterTemplate', 'contact', 'user'])->latest()->paginate(10);
        return view('letter_documents.index', compact('documents'));
    }

    public function create()
    {
        return view('letter_documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'letter_template_id' => 'required|exists:letter_templates,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'variables' => 'nullable|array'
        ]);

        $template = \App\Models\LetterTemplate::find($request->letter_template_id);

        $generate = \App\Services\LetterNumberService::generateNextNumber($template);

        $document = LetterDocument::create([
            'letter_template_id' => $template->id,
            'contact_id' => $request->contact_id,
            'user_id' => auth()->id(),
            'letter_number' => $generate['letter_number'],
            'sequence' => $generate['sequence'],
            'month' => $generate['month'],
            'year' => $generate['year'],
            'variables' => $request->variables ?? [],
        ]);

        return redirect()->route('letter-documents.show', $document->id)
            ->with('success', 'Letter generated successfully!');
    }

    public function show(LetterDocument $letterDocument)
    {
        return view('letter_documents.show', compact('letterDocument'));
    }

    public function print(LetterDocument $letterDocument)
    {
        return view('letter_documents.print', compact('letterDocument'));
    }

    public function envelope(LetterDocument $letterDocument)
    {
        return view('letter_documents.envelope', compact('letterDocument'));
    }

    public function edit(LetterDocument $letterDocument)
    {
        return view('letter_documents.edit', compact('letterDocument'));
    }

    public function update(Request $request, LetterDocument $letterDocument)
    {
        // Implementation for updating generated letter
    }

    public function destroy(LetterDocument $letterDocument)
    {
        $letterDocument->delete();
        return redirect()->route('letter-documents.index')->with('success', 'Document deleted successfully.');
    }
}
