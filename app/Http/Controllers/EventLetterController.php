<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Event;
use App\Models\EventLetter;
use App\Models\LetterAsset;
use App\Models\LetterTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;

class EventLetterController extends Controller
{
    public function index(Event $event)
    {
        $letters = $event->letters()->latest()->paginate(10);

        return view('events.letters.index', compact('event', 'letters'));
    }

    public function create(Event $event)
    {
        $preview = EventLetter::generateForEvent($event);
        $nextSequence = $preview['sequence'];
        $previewLetterNumber = $preview['letter_number'];
        $defaultCity = AppSetting::get('org_city_default', config('organization.city', 'Jakarta'));

        $logos = LetterAsset::logos()->latest()->get();
        $kops = LetterAsset::kops()->latest()->get();
        $ttds = LetterAsset::ttds()->latest()->get();

        return view('events.letters.create', compact('event', 'nextSequence', 'previewLetterNumber', 'defaultCity', 'logos', 'kops', 'ttds'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'body' => 'required|string',
            'event_code' => 'nullable|string|max:15',
            'issued_at' => 'required|date',
            'city' => 'required|string|max:255',
            'logo_asset_id' => 'nullable|exists:letter_assets,id',
            'kop_asset_id' => 'nullable|exists:letter_assets,id',
            'ttd_asset_id' => 'nullable|exists:letter_assets,id',
            'sig_text_above' => 'nullable|string|max:255',
            'sig_name' => 'nullable|string|max:255',
            'sig_position' => 'nullable|string|max:255',
        ]);

        $letter = new EventLetter($validated);
        $letter->event_id = $event->id;

        $generated = EventLetter::generateForEvent($event, $request->input('event_code'));
        $letter->letter_number = $generated['letter_number'];
        $letter->letter_sequence = $generated['sequence'];

        $letter->save();

        return redirect()->route('letters.index', $event)->with('success', 'Letter created successfully.');
    }

    public function edit(Event $event, EventLetter $letter)
    {
        $logos = LetterAsset::logos()->latest()->get();
        $kops = LetterAsset::kops()->latest()->get();
        $ttds = LetterAsset::ttds()->latest()->get();

        return view('events.letters.edit', compact('event', 'letter', 'logos', 'kops', 'ttds'));
    }

    public function update(Request $request, Event $event, EventLetter $letter)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'body' => 'required|string',
            'issued_at' => 'required|date',
            'city' => 'required|string|max:255',
            'logo_asset_id' => 'nullable|exists:letter_assets,id',
            'kop_asset_id' => 'nullable|exists:letter_assets,id',
            'ttd_asset_id' => 'nullable|exists:letter_assets,id',
            'sig_text_above' => 'nullable|string|max:255',
            'sig_name' => 'nullable|string|max:255',
            'sig_position' => 'nullable|string|max:255',
        ]);

        $letter->update($validated);

        return redirect()->route('letters.index', $event)->with('success', 'Letter updated successfully.');
    }

    public function destroy(Event $event, EventLetter $letter)
    {
        if ($letter->signature_path) {
            Storage::disk('public')->delete($letter->signature_path);
        }
        $letter->delete();

        return redirect()->route('letters.index', $event)->with('success', 'Letter deleted successfully.');
    }

    public function exportPdf(EventLetter $letter)
    {
        $event = $letter->event;
        $orgSettings = AppSetting::getOrg();
        $pdf = Pdf::loadView('events.letters.pdf', compact('event', 'letter', 'orgSettings'));

        return $pdf->download('Surat_'.str_replace(' ', '_', $letter->recipient_name).'.pdf');
    }

    public function bulkGenerate(Event $event)
    {
        $templates = LetterTemplate::latest()->get();

        return view('events.letters.bulk-generate', compact('event', 'templates'));
    }

    public function bulkStore(Request $request, Event $event)
    {
        $request->validate([
            'template_id' => 'required|exists:letter_templates,id',
            'manual_names' => 'nullable|string',
            'excel_file' => 'nullable|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $template = LetterTemplate::findOrFail($request->template_id);
        $names = collect();

        // 1. Process manual names
        if ($request->filled('manual_names')) {
            $manualNames = explode("\n", str_replace("\r", '', $request->manual_names));
            foreach ($manualNames as $name) {
                $cleanName = trim($name);
                if (! empty($cleanName)) {
                    $names->push($cleanName);
                }
            }
        }

        // 2. Process Excel/CSV file
        if ($request->hasFile('excel_file')) {
            try {
                $file = $request->file('excel_file');
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();

                foreach ($sheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    foreach ($cellIterator as $cell) {
                        $value = trim($cell->getValue() ?? '');
                        if (! empty($value)) {
                            $names->push($value);
                        }
                        break; // Only read the first column (Column A)
                    }
                }
            } catch (\Exception $e) {
                return back()->withErrors(['excel_file' => 'Error reading file: '.$e->getMessage()]);
            }
        }

        $names = $names->unique()->values();

        if ($names->isEmpty()) {
            return back()->withErrors(['names' => 'No names provided. Please enter manual names or upload a valid file.']);
        }

        $orgSettings = AppSetting::getOrg();
        $zipPath = storage_path('app/private/Bulk_Letters_'.time().'.zip');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {

            foreach ($names as $name) {
                // Generate next sequence for this event
                $generated = EventLetter::generateForEvent($event, null);

                // Customize body with placeholder
                $body = str_replace('{nama}', $name, $template->body);

                // Create new letter from template
                $newLetter = new EventLetter([
                    'event_id' => $event->id,
                    'title' => $template->title,
                    'recipient_name' => $name,
                    'recipient_phone' => null,
                    'body' => $body,
                    'issued_at' => now(), // Default to today
                    'city' => $orgSettings['city_default'] ?? 'Jakarta',
                    'logo_asset_id' => $template->logo_asset_id,
                    'kop_asset_id' => $template->kop_asset_id,
                    'ttd_asset_id' => $template->ttd_asset_id,
                    'sig_text_above' => $template->sig_text_above,
                    'sig_name' => $template->sig_name,
                    'sig_position' => $template->sig_position,
                    'letter_number' => $generated['letter_number'],
                    'letter_sequence' => $generated['sequence'],
                ]);
                $newLetter->save();

                // Generate PDF
                $pdf = Pdf::loadView('events.letters.pdf', ['event' => $event, 'letter' => $newLetter, 'orgSettings' => $orgSettings]);
                $pdfContent = $pdf->output();

                $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $name);
                $filename = 'Surat_'.$safeName.'_'.$generated['sequence'].'.pdf';
                $zip->addFromString($filename, $pdfContent);
            }

            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return back()->withErrors(['zip' => 'Failed to create ZIP archive.']);
    }
}
