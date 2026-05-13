<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_donor_id',
        'donated_at',
        'location',
        'notes',
    ];

    protected $casts = [
        'donated_at' => 'date',
    ];

    public function bloodDonor()
    {
        return $this->belongsTo(BloodDonor::class);
    }
}
