<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Event;
use App\Models\EventLetter;
use App\Models\LetterTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

class EventLetterService
{
    /**
     * Parse nama-nama penerima dari input manual dan/atau file Excel/CSV.
     *
     * @return Collection<int, string>
     */
    public function parseNames(?string $manualNames = null, ?UploadedFile $file = null): Collection
    {
        $names = collect();

        if (filled($manualNames)) {
            $lines = explode("\n", str_replace("\r", '', $manualNames));
            foreach ($lines as $line) {
                $clean = trim($line);
                if ($clean !== '') {
                    $names->push($clean);
                }
            }
        }

        if ($file !== null) {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $value = trim($cell->getValue() ?? '');
                    if ($value !== '') {
                        $names->push($value);
                    }
                    break; // Hanya kolom A
                }
            }
        }

        return $names->unique()->values();
    }

    /**
     * Generate the next sequence and letter number for an event.
     * Optionally accept a custom event code, otherwise generate from event name.
     */
    public function generateForEvent(Event $event, ?string $customCode = null): array
    {
        // Find highest sequence for this event
        $maxSequence = EventLetter::where('event_id', $event->id)->max('letter_sequence') ?? 0;
        $nextSequence = $maxSequence + 1;

        // Generate Roman numeral for month
        $romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $monthRoman = $romans[date('n') - 1];

        $year = date('Y');

        if (! $customCode) {
            // Generate initials from event name (e.g., "Donor Darah Masal" -> DDM)
            $words = explode(' ', $event->name);
            $initials = '';
            foreach ($words as $word) {
                if (strlen($word) > 0) {
                    $initials .= strtoupper(substr($word, 0, 1));
                }
            }
            $customCode = $initials;
        }

        // Format: 001/CDM/DDM/V/2026
        $formattedSequence = str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT);
        $letterNumber = "{$formattedSequence}/CDM/{$customCode}/{$monthRoman}/{$year}";

        return [
            'sequence' => $nextSequence,
            'letter_number' => $letterNumber,
        ];
    }

    /**
     * Generate surat massal untuk semua nama, simpan ke DB, buat PDF, dan kembalikan path ZIP.
     */
    public function bulkGenerate(Event $event, LetterTemplate $template, Collection $names): string
    {
        $orgSettings = AppSetting::getOrg();
        $zipPath = storage_path('app/private/Bulk_Letters_'.time().'.zip');
        $zip = new ZipArchive;

        $zip->open($zipPath, ZipArchive::CREATE);

        foreach ($names as $name) {
            $generated = $this->generateForEvent($event, null);
            $body = str_replace('{nama}', $name, $template->body);

            $letter = new EventLetter([
                'event_id' => $event->id,
                'title' => $template->title,
                'recipient_name' => $name,
                'recipient_phone' => null,
                'body' => $body,
                'issued_at' => now(),
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
            $letter->save();

            $pdf = Pdf::loadView('events.letters.pdf', [
                'event' => $event,
                'letter' => $letter,
                'orgSettings' => $orgSettings,
            ]);
            $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $name);
            $filename = 'Surat_'.$safeName.'_'.$generated['sequence'].'.pdf';
            $zip->addFromString($filename, $pdf->output());
        }

        $zip->close();

        return $zipPath;
    }
}
