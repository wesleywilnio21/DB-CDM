<?php

namespace App\Imports;

use App\Models\Contact;
use App\Services\ActivityLogger;

class ContactsImport
{
    /**
     * Import contacts from an uploaded XLSX/XLS file.
     * Upserts based on the Phone number.
     */
    public function upload($filePath)
    {
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Remove header

        // name, phone, birthday, address, organization, emails, note

        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (empty($row[0])) {
                continue;
            } // Skip if Name is empty

            $name = trim($row[0]);
            $phones = array_filter(array_map('trim', explode(',', $row[1] ?? '')));
            $birthday = ! empty($row[2]) ? $row[2] : null;
            $address = $row[3] ?? null;
            $organization = $row[4] ?? null;
            $email = $row[5] ?? null;
            $notes = $row[6] ?? null;

            // Find contact by name (deduplication based on name)
            $contact = Contact::where('name', $name)->first();

            if ($contact) {
                // Update existing
                $contact->update([
                    'email' => $email,
                    'address' => $address,
                    'organization' => $organization,
                    'birthdate' => $birthday,
                    'notes' => $notes,
                ]);

                ActivityLogger::log('updated', $contact, "Updated contact via import: {$contact->name}");
            } else {
                // Create new
                $contact = Contact::create([
                    'name' => $name,
                    'email' => $email,
                    'address' => $address,
                    'organization' => $organization,
                    'birthdate' => $birthday,
                    'notes' => $notes,
                ]);

                ActivityLogger::log('created', $contact, "Imported new contact: {$contact->name}");
                $count++;
            }

            // Sync phones
            foreach ($phones as $index => $phoneStr) {
                if (! empty($phoneStr)) {
                    $contact->phones()->firstOrCreate(
                        ['phone' => $phoneStr],
                        ['is_primary' => ($index === 0 && $contact->phones()->count() === 0)]
                    );
                }
            }
        }

        fclose($file);

        return $count;
    }
}
