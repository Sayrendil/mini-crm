<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'files.*' => 'file|max:5120',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $exists = \App\Models\Ticket::whereHas('customer', function ($q) {
                $q->where('email', $this->email)->orWhere('phone', $this->phone);
            })
                ->where('created_at', '>=', now()->subDay())
                ->exists();

            if ($exists) {
                $validator->errors()->add('limit', 'Вы уже отправляли заявку в последние 24 часа.');
            }
        });
    }
}
