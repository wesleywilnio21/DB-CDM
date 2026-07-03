<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity as ConcernsLogsActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Event extends Model
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
            ->setDescriptionForEvent(fn(string $eventName) => "This event has been {$eventName}");
    }
    protected $fillable = [
        'name', 'date', 'location', 'description'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }
}
