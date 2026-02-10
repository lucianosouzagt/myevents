<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarbecuePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'men' => ['required', 'integer', 'min:0', 'max:1000'],
            'women' => ['required', 'integer', 'min:0', 'max:1000'],
            'children' => ['required', 'integer', 'min:0', 'max:1000'],
            'types' => ['nullable', 'array'],
            'types.*' => ['integer', 'exists:barbecue_item_types,id'],
        ];
    }
}
