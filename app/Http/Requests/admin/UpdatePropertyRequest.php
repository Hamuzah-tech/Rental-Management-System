<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'landlord_id' => [
                'required',
                'exists:users,id'
            ],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            'description' => [
                'nullable',
                'string'
            ],

        ];
    }
}