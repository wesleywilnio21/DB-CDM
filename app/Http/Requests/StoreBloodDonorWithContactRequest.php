<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBloodDonorWithContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'phone'              => 'required|string|max:255|unique:contacts',
            'email'              => 'nullable|email|max:255',
            'organization'       => 'nullable|string|max:255',
            'blood_type'         => 'required|in:A,B,AB,O',
            'rhesus'             => 'required|in:+,-',
            'last_donation_date' => 'nullable|date',
        ];
    }
}
