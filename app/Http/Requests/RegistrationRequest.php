<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'first_name' => 'required|regex:/^[a-zA-Z0-9\d\-_.,\s]+$/u|max:191',
            'last_name' => 'required|regex:/^[a-zA-Z0-9\d\-_.,\s]+$/u|max:191',
            'email' => 'required|email:filter,strict,dns|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'date_of_birth' => 'required|before:18 years ago',
            'country_of_residence' => 'required',
            'terms_conditions' => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ];
    }

    /**
     * Custom validation message
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.regex'=> _lang('First name must be in english characters (a-z,A-Z,0-9)'),
            'last_name.regex'=> _lang('Last name must be in english characters (a-z,A-Z,0-9)'),
            'email.email' => _lang('The email address must be valid.'),
            'g-recaptcha-response.required'=> _lang('Please verify the captcha.'),
            'date_of_birth.before'=> _lang('You must be 18 years old and above'),
            'terms_conditions.required'=> _lang('Please indicate that you have read and agree to the Terms and Conditions and Privacy Policy.'),
        ];
    }
}
