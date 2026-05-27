<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationSessionDonor extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_session_id', 'blood_donor_id', 'donated_at', 'location', 'notes'
    ];

    protected $casts = [
        'donated_at' => 'date',
    ];

    public function donationSession()
    {
        return $this->belongsTo(DonationSession::class);
    }

    public function bloodDonor()
    {
        return $this->belongsTo(BloodDonor::class);
    }
}
