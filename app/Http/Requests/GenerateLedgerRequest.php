<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateLedgerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dateFrom' => 'required|date_format:Y-m-d',
            'dateTo' => 'required|date_format:Y-m-d',
            'customerId' => 'nullable|exists:customers,id'
        ];
    }

    public function messages()
    {
        return [
            'dateFrom.required' => trans('validation.custom.date.required'),
            'dateFrom.date_format' => trans('validation.custom.date.date_format'),
            'dateTo.required' => trans('validation.custom.date.required'),
            'dateTo.date_format' => trans('validation.custom.date.date_format'),
            'customerId.exists' => trans('validation.custom.customer_id.exists'),
        ];
    }
}
