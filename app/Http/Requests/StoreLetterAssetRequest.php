<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLetterAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:logo,kop,ttd',
            'name' => 'required|string|max:255',
            'file' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ];
    }
}
