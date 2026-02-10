<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'whatsapp_message_template' => 'nullable|string|max:1000',
            'invitation_image' => 'nullable|image|mimes:jpeg,png,gif|max:5120', // Max 5MB
            'location' => 'required|string|max:255',
            'location_url' => 'nullable|url|max:500',
            'google_maps_link' => 'nullable|url|max:500',
            'start_time' => 'required|date|after:now',
            'has_end_time' => 'sometimes|boolean',
            'end_time' => 'nullable|required_if:has_end_time,1|date|after:start_time',
            'rsvp_deadline' => 'nullable|date|before:start_time',
            'capacity' => 'required|integer|min:1',
            'is_public' => 'boolean',
        ];
    }
}
