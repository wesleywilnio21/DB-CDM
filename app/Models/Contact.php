<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'address', 'organization', 'notes', 'birthdate',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function phones(): HasMany
    {
        return $this->hasMany(ContactPhone::class);
    }

    protected function primaryPhone(): Attribute
    {
        return Attribute::make(
            get: function () {
                $primary = $this->phones->where('is_primary', true)->first();

                return $primary ? $primary->phone : ($this->phones->first()->phone ?? null);
            },
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: function () {
                $primary = $this->phones->where('is_primary', true)->first();

                return $primary ? $primary->phone : ($this->phones->first()->phone ?? null);
            },
        );
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bloodDonor(): HasOne
    {
        return $this->hasOne(BloodDonor::class);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['search'] ?? null,
            function (Builder $query, mixed $search): void {
                $query->where(function (Builder $q) use ($search): void {
                    $q->where('name', 'like', '%'.(string) $search.'%')
                        ->orWhereHas('phones', function (Builder $pq) use ($search): void {
                            $pq->where('phone', 'like', '%'.(string) $search.'%');
                        });
                });
            }
        )->when(
            $filters['tag'] ?? null,
            function (Builder $query, mixed $tag): void {
                $query->whereHas('tags', function (Builder $q) use ($tag): void {
                    $q->where('tags.id', $tag);
                });
            }
        )->when(
            $filters['event'] ?? null,
            function (Builder $query, mixed $event): void {
                $query->whereHas('events', function (Builder $q) use ($event): void {
                    $q->where('events.id', $event);
                });
            }
        );
    }
}
