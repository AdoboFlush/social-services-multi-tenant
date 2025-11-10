<?php

namespace App\Http\Requests;

use App\Services\MemberCode\MemberCodeFacade;
use Illuminate\Foundation\Http\FormRequest;

class MemberCodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'member_code' => [
                "required",
                "min:6",
                function ($attribute, $value, $fail){
                    if(!MemberCodeFacade::checkMemberCode(request()->member_code)){
                        $fail(_lang("Member code does not exist."));
                    }
                }
         ],          
        ];
    }
}
