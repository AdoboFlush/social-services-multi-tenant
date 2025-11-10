<?php

namespace App\Http\Requests;

use App\Services\MemberCode\MemberCodeFacade;
use Illuminate\Foundation\Http\FormRequest;

class MemberValidateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|regex:/^[ñÑa-zA-Z0-9\d\-_.,\s]+$/u|max:191',
            'middle_name' => 'max:191',
            'last_name' => 'required|regex:/^[ñÑa-zA-Z0-9\d\-_.,\s]+$/u|max:191',
            'birth_date' => 'required|before:18 years ago',
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
            'member_code' => ["required", function ($attribute, $value, $fail) {
                if (!MemberCodeFacade::validateMemberRegistration(request())) {
                    $fail("Member code  does not match with information provided.");
                }
            }],
        ];
    }
}
