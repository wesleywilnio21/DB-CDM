<?php

namespace App\Exports;

use App\Models\BloodDonor;

class BloodDonorsExport
{
    public function download()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="blood_donors_export_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: max-age=0');

        $out = fopen('php://output', 'w');
        
        // Headers
        fputcsv($out, ['Contact Name', 'Blood Type', 'Rhesus', 'Last Donation Date']);

        $donors = BloodDonor::with('contact')->get();
        foreach ($donors as $donor) {
            fputcsv($out, [
                $donor->contact->name,
                $donor->blood_type,
                $donor->rhesus,
                $donor->last_donation_date ? $donor->last_donation_date->format('Y-m-d') : ''
            ]);
        }

        fclose($out);
        exit;
    }

    public function template()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="blood_donors_import_template.csv"');
        header('Cache-Control: max-age=0');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Contact Name', 'Blood Type', 'Rhesus', 'Last Donation Date']);
        fputcsv($out, ['John Doe', 'O', '+', '2023-01-01']);
        
        fclose($out);
        exit;
    }
}
