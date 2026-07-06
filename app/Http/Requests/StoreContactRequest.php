<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phones' => 'required|array|min:1',
            'phones.*' => 'required|numeric|distinct|unique:contact_phones,phone',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'organization' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'events' => 'nullable|array',
            'events.*' => 'exists:events,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'phones.*' => 'phone number',
        ];
    }
}
