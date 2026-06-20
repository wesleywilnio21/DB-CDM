<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAndAddContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'guest_count'  => 'nullable|integer|min:0',
        ];
    }
}
