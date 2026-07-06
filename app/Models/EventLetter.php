<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function logoAsset(): BelongsTo
    {
        return $this->belongsTo(LetterAsset::class, 'logo_asset_id');
    }

    public function kopAsset(): BelongsTo
    {
        return $this->belongsTo(LetterAsset::class, 'kop_asset_id');
    }

    public function ttdAsset(): BelongsTo
    {
        return $this->belongsTo(LetterAsset::class, 'ttd_asset_id');
    }

    // Removed generateForEvent and romanMonth, logic moved to EventLetterService

    protected function formattedIssuedAtId(): Attribute
    {
        return Attribute::make(
            get: function () {
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
        );
    }

    protected function displayCityDate(): Attribute
    {
        return Attribute::make(
            get: function () {
                $city = $this->city ?: AppSetting::get('org_city_default', config('organization.city', 'Jakarta'));
                $date = $this->formatted_issued_at_id ?: now()->format('d M Y');

                return "{$city}, {$date}";
            }
        );
    }
}
