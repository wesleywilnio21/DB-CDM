<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone'=> 'nullable|string|max:255',
            'body'           => 'required|string',
            'issued_at'      => 'required|date',
            'city'           => 'required|string|max:255',
            'logo_asset_id'  => 'nullable|exists:letter_assets,id',
            'kop_asset_id'   => 'nullable|exists:letter_assets,id',
            'ttd_asset_id'   => 'nullable|exists:letter_assets,id',
            'sig_text_above' => 'nullable|string|max:255',
            'sig_name'       => 'nullable|string|max:255',
            'sig_position'   => 'nullable|string|max:255',
        ];
    }
}
