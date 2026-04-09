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
            'type'            => 'required|in:owed,paid',
            'note'            => 'nullable|string',
            'attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
