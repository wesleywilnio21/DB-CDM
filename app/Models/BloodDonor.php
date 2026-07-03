<?php

namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity as ConcernsLogsActivity;

class BloodDonor extends Model
{
    use ConcernsLogsActivity;
    use SoftDeletes;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "This BloodDonor has been {$eventName}");
    }
}
