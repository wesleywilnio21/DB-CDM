<?php

namespace App\Imports;

use App\Models\Contact;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ContactsImport
{
    /**
     * Import contacts from an uploaded XLSX/XLS file.
     * Upserts based on the Phone number.
     */
    public function upload($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header = array_shift($rows); // Remove header
        $count = 0;

        foreach ($rows as $row) {
            if (empty($row[0]) || empty($row[1])) continue; // Skip if Name or Phone is empty

            $data = [
                'name'         => $row[0],
                'phone'        => (string)$row[1],
                'email'        => $row[2],
                'address'      => $row[3],
                'organization' => $row[4],
                'birthdate'    => $row[5],
                'notes'        => $row[6],
            ];

            // Basic validation
            $validator = Validator::make($data, [
                'name'  => 'required|string',
                'phone' => 'required',
            ]);

            if ($validator->fails()) continue;

            // Upsert by phone
            $contact = Contact::updateOrCreate(
                ['phone' => $data['phone']],
                [
                    'name'         => $data['name'],
                    'email'        => $data['email'],
                    'address'      => $data['address'],
                    'organization' => $data['organization'],
                    'birthdate'    => $data['birthdate'],
                    'notes'        => $data['notes'],
                ]
            );

            if ($contact->wasRecentlyCreated) {
                ActivityLogger::log('created', $contact, "Imported new contact: {$contact->name}");
            } else {
                ActivityLogger::log('updated', $contact, "Updated contact via import: {$contact->name}");
            }

            $count++;
        }

        return $count;
    }
}
