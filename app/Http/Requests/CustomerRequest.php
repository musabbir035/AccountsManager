<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Custom form request for adding & updating Customers
class CustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // General rules when adding new customers.
        $rules = [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:11|unique:customers|regex:/(01)[0-9]{9}/',
            'email' => 'nullable|email|unique:customers',
            'address' => 'nullable|string',
            'initial_balance' => 'nullable|numeric',
            'type' => 'nullable|integer|between:1,2'
        ];

        // Updated rules for 'mobile' & 'email' when updating 
        // (PUT or PATCH request method) an existing customer
        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['mobile'] = [
                'required',
                'string',
                'max:11',
                'regex:/(01)[0-9]{9}/',
                'unique:customers,mobile,' . $this->id
            ];
            $rules['email'] = [
                'nullable',
                'email',
                'unique:customers,email,' . $this->id
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => trans('validation.custom.name.required'),
            'mobile.required' => trans('validation.custom.mobile.required'),
            'mobile.max' => trans('validation.custom.mobile.max'),
            'mobile.regex' => trans('validation.custom.mobile.regex'),
            'mobile.unique' => trans('validation.mobile.customer.unique'),
            'email.unique' => trans('validation.email.customer.unique'),
            'initial_balance.numeric' => trans('validation.custom.balance.numeric'),
            'type.required' => trans('validation.custom.type.required'),
            'type.integer' => trans('validation.custom.type.integer'),
            'type.between' => trans('validation.custom.type.between')
        ];
    }
}
