<?php
namespace App\Http\Requests;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
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
    public function rules($id)
    {
        return [
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'email' => 'required|email|unique:users,email,' .$id,
            'phone' => 'required|unique:users,phone,' .$id,
            'password' => 'nullable|max:20|min:6|confirmed',
            'user_access' => 'required',
            'status' => 'required',
            'profile_picture' => 'nullable|image|max:5120'
        ];
    }
}
