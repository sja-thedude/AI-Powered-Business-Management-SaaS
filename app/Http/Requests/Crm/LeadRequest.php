<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized by the controller policy / route middleware.
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'company'         => ['nullable', 'string', 'max:255'],
            'email'           => ['nullable', 'email', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'source'          => ['required', Rule::in(['web', 'referral', 'ads', 'manual', 'import'])],
            'status'          => ['required', Rule::in(['new', 'contacted', 'qualified', 'unqualified', 'converted'])],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'owner_id'        => ['nullable', 'integer', 'exists:users,id'],
            'notes'           => ['nullable', 'string'],
        ];
    }
}
