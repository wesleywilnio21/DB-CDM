<?php

namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity as ConcernsLogsActivity;

class DonationHistory extends Model
{
    use ConcernsLogsActivity;
    use SoftDeletes;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "This DonationHistory has been {$eventName}");
    }
}
