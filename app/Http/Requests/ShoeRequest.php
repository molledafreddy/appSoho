<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShoeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  => 'required',
            'color' => 'required',
            'size'  => 'required|numeric',
            'price' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => 'The Name is required.',
            'color.required' => 'The Color is required.',
            'size.required'  => 'The Size is required.',
            'price.required' => 'The Price is required.',
            'size.numer'     => 'the data must be numbered.'
        ];
    }
}
