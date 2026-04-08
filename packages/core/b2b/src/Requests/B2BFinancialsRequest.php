<?php

namespace Core\B2B\Requests;

use Illuminate\Foundation\Http\FormRequest;

class B2BFinancialsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id'      => 'required|exists:companies,id',
            'reference_id'    => 'nullable|string|max:255',
            'collection_date' => 'nullable|date',
            'amount'          => 'required|numeric',
            'note'            => 'nullable|string',
        ];
    }
}
