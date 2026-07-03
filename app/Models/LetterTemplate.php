<?php

namespace App\Models;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity as ConcernsLogsActivity;

class LetterTemplate extends Model
{
    use ConcernsLogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'content',
        'number_format',
        'signatory_name',
        'signatory_position',
        'signature_image',
        'stamp_image'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn(string $eventName) => "This LetterTemplate has been {$eventName}");
    }
}
