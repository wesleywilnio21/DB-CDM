<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'date', 'location', 'description'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class)->withPivot('guest_count');
    }

    public function letters()
    {
        return $this->hasMany(EventLetter::class);
    }
}
