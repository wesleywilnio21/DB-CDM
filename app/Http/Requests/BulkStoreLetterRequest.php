<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_id'  => 'required|exists:letter_templates,id',
            'manual_names' => 'nullable|string',
            'excel_file'   => 'nullable|file|mimes:xlsx,xls,csv|max:5120',
        ];
    }
}
