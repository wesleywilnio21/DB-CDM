<?php

namespace App\Exports;

use App\Models\Contact;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ContactsExport
{
    /**
     * Export all contacts to a downloadable XLSX file.
     */
    public function download()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="contacts_export_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: max-age=0');

        $out = fopen('php://output', 'w');
        
        // Headers: name, phone, birthday, address, organization, emails, note
        fputcsv($out, ['name', 'phone', 'birthday', 'address', 'organization', 'emails', 'note']);

        $contacts = Contact::with('phones')->get();
        foreach ($contacts as $contact) {
            $phones = $contact->phones->pluck('phone')->join(', ');
            fputcsv($out, [
                $contact->name,
                $phones,
                $contact->birthdate ? $contact->birthdate->format('Y-m-d') : '',
                $contact->address,
                $contact->organization,
                $contact->email,
                $contact->notes
            ]);
        }

        fclose($out);
        exit;
    }

    public function template()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="contacts_import_template.csv"');
        header('Cache-Control: max-age=0');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['name', 'phone', 'birthday', 'address', 'organization', 'emails', 'note']);
        fputcsv($out, ['John Doe', '08123456789, 08987654321', '1990-01-01', 'Jl. Sudirman No 1', 'PT ABC', 'john@example.com', 'VIP Customer']);
        
        fclose($out);
        exit;
    }
}
