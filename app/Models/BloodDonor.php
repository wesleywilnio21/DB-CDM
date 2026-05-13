<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BloodDonor extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id', 'blood_type', 'rhesus', 'last_donation_date'
    ];

    protected $casts = [
        'last_donation_date' => 'date',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function getNextEligibleDateAttribute()
    {
        if (!$this->last_donation_date) return null;
        return Carbon::parse($this->last_donation_date)->addDays(60);
    }

    public function donationHistories()
    {
        return $this->hasMany(DonationHistory::class)->orderBy('donated_at', 'desc');
    }
}
