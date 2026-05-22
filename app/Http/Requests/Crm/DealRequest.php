<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'pipeline_id'         => ['required', 'integer', 'exists:pipelines,id'],
            'pipeline_stage_id'   => ['required', 'integer', 'exists:pipeline_stages,id'],
            'client_id'           => ['nullable', 'integer', 'exists:clients,id'],
            'owner_id'            => ['nullable', 'integer', 'exists:users,id'],
            'value'               => ['nullable', 'numeric', 'min:0'],
            'currency'            => ['nullable', 'string', 'size:3'],
            'status'              => ['nullable', Rule::in(['open', 'won', 'lost'])],
            'probability'         => ['nullable', 'integer', 'between:0,100'],
            'expected_close_date' => ['nullable', 'date'],
        ];
    }
}
