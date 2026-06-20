<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddContactToEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_ids'   => 'required|array',
            'contact_ids.*' => 'exists:contacts,id',
            'guest_counts'  => 'nullable|array',
            'guest_counts.*'=> 'integer|min:0',
        ];
    }
}
