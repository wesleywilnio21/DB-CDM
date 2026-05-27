<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'address', 'organization', 'notes', 'birthdate'
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function phones()
    {
        return $this->hasMany(ContactPhone::class);
    }

    public function getPrimaryPhoneAttribute()
    {
        $primary = $this->phones->where('is_primary', true)->first();
        return $primary ? $primary->phone : ($this->phones->first()->phone ?? null);
    }

    public function getPhoneAttribute()
    {
        return $this->primary_phone;
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bloodDonor()
    {
        return $this->hasOne(BloodDonor::class);
    }
}
