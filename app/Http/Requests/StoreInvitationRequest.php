<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['superadmin', 'admin'], true);
    }

    public function rules(): array
    {
        $allowedRoles = $this->user()->isSuperAdmin() ? ['admin'] : ['admin', 'member'];

        $rules = [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in($allowedRoles)],
        ];

        if ($this->user()->isSuperAdmin()) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email already exists.',
            'company_name.required' => 'Please enter a name for the new company.',
        ];
    }
}
