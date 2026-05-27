<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    protected $fillable = [
        'title',
        'body',
        'logo_asset_id',
        'kop_asset_id',
        'ttd_asset_id',
        'sig_text_above',
        'sig_name',
        'sig_position',
    ];

    public function logoAsset()
    {
        return $this->belongsTo(LetterAsset::class, 'logo_asset_id');
    }

    public function kopAsset()
    {
        return $this->belongsTo(LetterAsset::class, 'kop_asset_id');
    }

    public function ttdAsset()
    {
        return $this->belongsTo(LetterAsset::class, 'ttd_asset_id');
    }
}
