<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity as ConcernsLogsActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Contact extends Model
{
    use SoftDeletes;

    use HasFactory, ConcernsLogsActivity;

    protected $guarded = [];

    // Konfigurasi Spatie Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // akan log semua attribute
            ->logOnlyDirty() // hanya log yang berubah 
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "This contact has been {$eventName}");
    }

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
