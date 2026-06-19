<?php

namespace App\Imports;

use App\Models\BloodDonor;
use App\Models\Contact;
use App\Services\ActivityLogger;

class BloodDonorsImport
{
    public function upload($filePath)
    {
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            if (empty($row[0])) {
                continue;
            } // Skip if Contact Name is empty

            $contactName = trim($row[0]);
            $bloodType = trim($row[1] ?? '');
            $rhesus = trim($row[2] ?? '');
            $lastDonationDate = ! empty($row[3]) ? trim($row[3]) : null;

            // Find contact by name
            $contact = Contact::where('name', $contactName)->first();

            // If contact doesn't exist, create it minimally
            if (! $contact) {
                $contact = Contact::create(['name' => $contactName]);
                ActivityLogger::log('created', $contact, "Created contact via Blood Donor import: {$contact->name}");
            }

            if (! empty($bloodType)) {
                $bloodDonor = BloodDonor::where('contact_id', $contact->id)->first();
                if ($bloodDonor) {
                    $bloodDonor->update([
                        'blood_type' => $bloodType,
                        'rhesus' => $rhesus,
                        'last_donation_date' => $lastDonationDate,
                    ]);
                    ActivityLogger::log('updated', $bloodDonor, "Updated Blood Donor via import for: {$contact->name}");
                } else {
                    $bloodDonor = BloodDonor::create([
                        'contact_id' => $contact->id,
                        'blood_type' => $bloodType,
                        'rhesus' => $rhesus,
                        'last_donation_date' => $lastDonationDate,
                    ]);
                    ActivityLogger::log('created', $bloodDonor, "Created Blood Donor via import for: {$contact->name}");
                }
                $count++;
            }
        }

        fclose($file);

        return $count;
    }
}
