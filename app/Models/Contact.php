<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'organization', 'notes', 'birthdate'
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

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
