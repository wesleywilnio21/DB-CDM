<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_date', 'location', 'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function getDisplayNameAttribute()
    {
        $dateStr = $this->session_date ? $this->session_date->format('M d, Y') : '';
        if ($this->location) {
            return $dateStr.' · '.$this->location;
        }

        return $dateStr;
    }

    public function donors()
    {
        return $this->belongsToMany(BloodDonor::class, 'donation_session_donors')
            ->withPivot('donated_at', 'location', 'notes', 'id')
            ->withTimestamps();
    }
}
