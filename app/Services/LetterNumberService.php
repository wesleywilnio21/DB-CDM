<?php

namespace App\Services;

use App\Models\LetterDocument;
use App\Models\LetterTemplate;
use Carbon\Carbon;

class LetterNumberService
{
    protected static $romawi = [
        1 => 'I',
        2 => 'II',
        3 => 'III',
        4 => 'IV',
        5 => 'V',
        6 => 'VI',
        7 => 'VII',
        8 => 'VIII',
        9 => 'IX',
        10 => 'X',
        11 => 'XI',
        12 => 'XII'
    ];

    public static function generateNextNumber(LetterTemplate $template, Carbon $date = null)
    {
        $date = $date ?? Carbon::now();
        $month = $date->month;
        $year = $date->year;
        $query = LetterDocument::where('month', $month)
            ->where('year', $year);

        // Cari urutan terakhir secara global pada bulan dan tahun yang sama
        $lastDoc = LetterDocument::where('month', $month)
            ->where('year', $year)
            ->orderBy('sequence', 'desc')
            ->first();

        $nextSequence = $lastDoc ? $lastDoc->sequence + 1 : 1;


        // dd($template->id, $lastDoc, $nextSequence, $query->toSql(), $query->getBindings());

        // Format nomor surat: 001/CDM/V/2026 => {nomor} / {bulan_romawi} / {tahun} dll
        $format = $template->number_format;

        $numberString = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        $bulanRomawi = self::$romawi[$month];

        // Replace placeholders
        $letterNumber = str_replace(
            ['{nomor}', '{bulan}', '{bulan_romawi}', '{tahun}', '{tanggal}'],
            [$numberString, str_pad($month, 2, '0', STR_PAD_LEFT), $bulanRomawi, $year, $date->format('d')],
            $format
        );

        return [
            'letter_number' => $letterNumber,
            'sequence' => $nextSequence,
            'month' => $month,
            'year' => $year
        ];
    }
}
