<?php

namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity as ConcernsLogsActivity;

class LetterDocument extends Model
{
    use ConcernsLogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'letter_template_id',
        'contact_id',
        'user_id',
        'letter_number',
        'sequence',
        'month',
        'year',
        'variables',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public function letterTemplate()
    {
        return $this->belongsTo(LetterTemplate::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "This LetterDocument has been {$eventName}");
    }
}
