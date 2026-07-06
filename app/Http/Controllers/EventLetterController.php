<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BulkStoreLetterRequest;
use App\Http\Requests\StoreEventLetterRequest;
use App\Http\Requests\UpdateEventLetterRequest;
use App\Models\AppSetting;
use App\Models\Event;
use App\Models\EventLetter;
use App\Models\LetterAsset;
use App\Models\LetterTemplate;
use App\Services\EventLetterService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EventLetterController extends Controller
{
    public function __construct(
        private readonly EventLetterService $eventLetterService
    ) {
        $this->authorizeSuperAdmin();
    }

    public function index(Event $event): View
    {
        $letters = $event->letters()->latest()->paginate(10);

        return view('events.letters.index', compact('event', 'letters'));
    }

    public function create(Event $event): View
    {
        $preview = $this->eventLetterService->generateForEvent($event);
        $nextSequence = $preview['sequence'];
        $previewLetterNumber = $preview['letter_number'];
        $defaultCity = AppSetting::get('org_city_default', config('organization.city', 'Jakarta'));

        $logos = LetterAsset::logos()->latest()->get();
        $kops = LetterAsset::kops()->latest()->get();
        $ttds = LetterAsset::ttds()->latest()->get();

        return view('events.letters.create', compact('event', 'nextSequence', 'previewLetterNumber', 'defaultCity', 'logos', 'kops', 'ttds'));
    }

    public function store(StoreEventLetterRequest $request, Event $event): RedirectResponse
    {
        $letter = new EventLetter($request->validated());
        $letter->event_id = $event->id;

        $generated = $this->eventLetterService->generateForEvent($event, $request->input('event_code'));
        $letter->letter_number = $generated['letter_number'];
        $letter->letter_sequence = $generated['sequence'];

        $letter->save();

        return redirect()->route('letters.index', $event)->with('success', 'Letter created successfully.');
    }

    public function edit(Event $event, EventLetter $letter): View
    {
        $logos = LetterAsset::logos()->latest()->get();
        $kops = LetterAsset::kops()->latest()->get();
        $ttds = LetterAsset::ttds()->latest()->get();

        return view('events.letters.edit', compact('event', 'letter', 'logos', 'kops', 'ttds'));
    }

    public function update(UpdateEventLetterRequest $request, Event $event, EventLetter $letter): RedirectResponse
    {
        $letter->update($request->validated());

        return redirect()->route('letters.index', $event)->with('success', 'Letter updated successfully.');
    }

    public function destroy(Event $event, EventLetter $letter): RedirectResponse
    {
        if ($letter->signature_path) {
            Storage::disk('public')->delete($letter->signature_path);
        }
        $letter->delete();

        return redirect()->route('letters.index', $event)->with('success', 'Letter deleted successfully.');
    }

    public function exportPdf(EventLetter $letter): Response
    {
        $event = $letter->event;
        $orgSettings = AppSetting::getOrg();
        $pdf = Pdf::loadView('events.letters.pdf', compact('event', 'letter', 'orgSettings'));

        return $pdf->download('Surat_'.str_replace(' ', '_', $letter->recipient_name).'.pdf');
    }

    public function bulkGenerate(Event $event): View
    {
        $templates = LetterTemplate::latest()->get();

        return view('events.letters.bulk-generate', compact('event', 'templates'));
    }

    public function bulkStore(BulkStoreLetterRequest $request, Event $event): RedirectResponse|BinaryFileResponse
    {
        $template = LetterTemplate::findOrFail($request->validated()['template_id']);

        try {
            $names = $this->eventLetterService->parseNames(
                $request->input('manual_names'),
                $request->hasFile('excel_file') ? $request->file('excel_file') : null,
            );
        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Error reading file: '.$e->getMessage()]);
        }

        if ($names->isEmpty()) {
            return back()->withErrors(['names' => 'No names provided. Please enter manual names or upload a valid file.']);
        }

        $zipPath = $this->eventLetterService->bulkGenerate($event, $template, $names);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
