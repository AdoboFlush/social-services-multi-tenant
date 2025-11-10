<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserDocumentRequest extends FormRequest
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
            'nid_passport' => 'required|mimes:jpeg,png,jpg,gif|max:1048',
            'electric_bill' => 'required|mimes:jpeg,png,jpg,gif|max:1048'
        ];
    }
}
