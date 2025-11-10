<?php

namespace App\Http\Requests;

use App\Services\MemberCode\MemberCodeFacade;
use Illuminate\Foundation\Http\FormRequest;

class MemberLoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_number' => 'required',
            'password' => 'required|min:8', 
        ];
    }
}
