<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BloodDonor extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'blood_type',
        'rhesus',
        'last_donation_date',
    ];

    protected $casts = [
        'last_donation_date' => 'date',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    protected function nextEligibleDate(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->last_donation_date) {
                    return null;
                }

                return Carbon::parse($this->last_donation_date)->addDays(60);
            }
        );
    }

    public function donationSessions(): BelongsToMany
    {
        return $this->belongsToMany(DonationSession::class, 'donation_session_donors')
            ->withPivot('donated_at', 'location', 'notes', 'id')
            ->withTimestamps()
            ->orderByPivot('donated_at', 'desc');
    }

    /**
     * Scope untuk donor yang sudah eligible (next_eligible_date sudah lewat).
     */
    /**
     * Scope untuk donor yang sudah eligible (sudah 60 hari atau lebih dari donasi terakhir).
     */
    public function scopeEligible(Builder $query): Builder
    {
        return $query->whereNotNull('last_donation_date')
            ->where('last_donation_date', '<=', now()->subDays(60));
    }
}
