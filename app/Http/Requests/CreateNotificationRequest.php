<?php
namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class CreateNotificationRequest extends FormRequest
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
            'title' => 'required',
            'content' => 'required',
            'language' => 'required',
            'publish_date' => 'required',
            'publish_time' => 'required'
        ];
    }
}
