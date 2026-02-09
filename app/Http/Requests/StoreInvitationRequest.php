<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email',
            'event_id' => 'required|exists:events,id',
        ];
    }
}
