<?php
namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class CreatePaymentRequest extends FormRequest
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
            'recipients_email' => 'required',
            'amount' => 'required|numeric',
            'currency' => 'required'
        ];
    }
}
