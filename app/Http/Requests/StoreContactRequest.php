<?php

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
            'phone' => 'required|string|max:255|unique:contacts',
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
}
