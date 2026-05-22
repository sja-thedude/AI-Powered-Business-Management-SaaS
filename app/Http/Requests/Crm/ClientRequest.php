<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'company'  => ['nullable', 'string', 'max:255'],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'website'  => ['nullable', 'url', 'max:255'],
            'status'   => ['required', Rule::in(['active', 'inactive', 'prospect'])],
            'address'  => ['nullable', 'string', 'max:1000'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'tags'     => ['nullable', 'array'],
            'tags.*'   => ['string', 'max:50'],
        ];
    }
}
