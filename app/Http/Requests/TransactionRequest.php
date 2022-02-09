<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'type' => 'required|integer|between:1,2',
            'date' => 'required|date_format:Y-m-d',
            'customer_id' => 'nullable|exists:customers,id',
            'receipt_id' => 'nullable|exists:receipts,id',
        ];
    }

    public function messages()
    {
        return [
            'description.required' => trans('validation.custom.description.required'),
            'amount.required' => trans('validation.custom.amount.required'),
            'type.required' => trans('validation.custom.type.required'),
            'type.between' =>  trans('validation.custom.type.between'),
            'date.required' => trans('validation.custom.date.required'),
            'date.date_format' => trans('validation.custom.date.date_format'),
            'customer_id.exists' => trans('validation.custom.customer_id.exists'),
            'receipt_id.exists' => trans('validation.custom.receipt_id.exists')
        ];
    }
}
