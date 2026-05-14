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
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Contacts');

        // Headers
        $headers = ['Name', 'Phone', 'Email', 'Address', 'Organization', 'Birthdate', 'Notes'];
        foreach ($headers as $index => $header) {
            $col = chr(65 + $index);
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
        }

        // Data
        $contacts = Contact::all();
        $row = 2;
        foreach ($contacts as $contact) {
            $sheet->setCellValue("A{$row}", $contact->name);
            $sheet->setCellValue("B{$row}", $contact->phone);
            $sheet->setCellValue("C{$row}", $contact->email);
            $sheet->setCellValue("D{$row}", $contact->address);
            $sheet->setCellValue("E{$row}", $contact->organization);
            $sheet->setCellValue("F{$row}", $contact->birthdate ? $contact->birthdate->format('Y-m-d') : '');
            $sheet->setCellValue("G{$row}", $contact->notes);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="contacts_export_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Generate a blank template for import.
     */
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Import Template');

        $headers = ['Name', 'Phone', 'Email', 'Address', 'Organization', 'Birthdate', 'Notes'];
        foreach ($headers as $index => $header) {
            $col = chr(65 + $index);
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD1E9F6');
        }

        // Example row
        $sheet->setCellValue('A2', 'John Doe');
        $sheet->setCellValue('B2', '08123456789');
        $sheet->setCellValue('C2', 'john@example.com');
        $sheet->getStyle('A2:G2')->getFont()->setItalic(true);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="contacts_import_template.xlsx"');
        
        $writer->save('php://output');
        exit;
    }
}
