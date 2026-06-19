<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'recipient_name',
        'recipient_phone',
        'body',
        'signature_path',
        'letter_number',
        'letter_sequence',
        'issued_at',
        'city',
        'logo_asset_id',
        'kop_asset_id',
        'ttd_asset_id',
        'sig_text_above',
        'sig_name',
        'sig_position',
    ];

    protected $casts = [
        'issued_at' => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function logoAsset()
    {
        return $this->belongsTo(LetterAsset::class, 'logo_asset_id');
    }

    public function kopAsset()
    {
        return $this->belongsTo(LetterAsset::class, 'kop_asset_id');
    }

    public function ttdAsset()
    {
        return $this->belongsTo(LetterAsset::class, 'ttd_asset_id');
    }

    /**
     * Generate the next sequence and letter number for an event.
     * Optionally accept a custom event code, otherwise generate from event name.
     */
    public static function generateForEvent(Event $event, ?string $customCode = null)
    {
        // Find highest sequence for this event
        $maxSequence = self::where('event_id', $event->id)->max('letter_sequence') ?? 0;
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
        $formattedSequence = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        $letterNumber = "{$formattedSequence}/CDM/{$customCode}/{$monthRoman}/{$year}";

        return [
            'sequence' => $nextSequence,
            'letter_number' => $letterNumber,
        ];
    }

    private static function romanMonth($month)
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$month] ?? 'I';
    }

    public function getFormattedIssuedAtIdAttribute()
    {
        if (! $this->issued_at) {
            return null;
        }
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $this->issued_at->format('d').' '.$months[(int) $this->issued_at->format('m')].' '.$this->issued_at->format('Y');
    }

    public function getDisplayCityDateAttribute()
    {
        $city = $this->city ?: AppSetting::get('org_city_default', config('organization.city', 'Jakarta'));
        $date = $this->formatted_issued_at_id ?: now()->format('d M Y');

        return "{$city}, {$date}";
    }
}
