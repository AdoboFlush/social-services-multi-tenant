<?php

namespace App\Http\Requests;

use App\Services\MemberCode\MemberCodeFacade;
use Illuminate\Foundation\Http\FormRequest;

class MemberResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',    
        ];
    }
}
