<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvitationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email' => 'required|string|email|max:255',
        ];

        if ($this->isMethod('post')) {
            $rules['email'] .= '|unique:invitations';
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['email'] .= '|exists:invitations,email';
        }

        return $rules;
    }
}
