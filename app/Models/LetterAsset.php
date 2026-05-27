<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LetterAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'file_path',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeLogos($query)
    {
        return $query->where('type', 'logo');
    }

    public function scopeKops($query)
    {
        return $query->where('type', 'kop');
    }

    public function scopeTtds($query)
    {
        return $query->where('type', 'ttd');
    }

    /**
     * Get the absolute path to the file for DomPDF to read directly from storage.
     */
    public function absolutePath(): ?string
    {
        $path = storage_path('app/private/' . $this->file_path);
        return file_exists($path) ? $path : null;
    }
}
