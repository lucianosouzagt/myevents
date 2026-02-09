<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'invitation_image' => 'nullable|image|mimes:jpeg,png,gif|max:5120', // Max 5MB
            'location' => 'sometimes|string|max:255',
            'google_maps_link' => 'nullable|url|max:500',
            'start_time' => 'sometimes|date',
            'has_end_time' => 'sometimes|boolean',
            'end_time' => 'nullable|required_if:has_end_time,1|date|after:start_time',
            'capacity' => 'sometimes|integer|min:1',
            'is_public' => 'boolean',
        ];
    }
}
