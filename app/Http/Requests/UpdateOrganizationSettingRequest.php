<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'org_name' => 'required|string|max:255',
            'org_address' => 'required|string|max:500',
            'org_phone' => 'required|string|max:255',
            'org_tagline' => 'nullable|string|max:255',
            'org_city_default' => 'required|string|max:255',
        ];
    }
}
