<?php

namespace Core\Financials\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinancialsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id'      => 'required_without:user_id|nullable|exists:companies,id',
            'user_id'         => 'required_without:company_id|nullable|exists:users,id',
            'reference_id'    => 'nullable|string|max:255',
            'collection_date' => 'nullable|date',
            'amount'          => 'required|numeric',
            'type'            => 'required|in:owed,paid',
            'note'            => 'nullable|string',
            'attachment'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
