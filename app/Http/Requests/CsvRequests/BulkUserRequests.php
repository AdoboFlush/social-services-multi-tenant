<?php
namespace App\Http\Requests\CsvRequests;

use Validator;

class BulkUserRequests
{
    const DATE = 0;
    const EMAIL = 1;
    const FIRST_NAME = 2;
    const LAST_NAME = 3;
    const DATE_OF_BIRTH = 4;
    const ACCOUNT_TYPE = 5;
    const PHONE = 6;
    const ADDRESS = 7;
    const CITY = 8;
    const STATE = 9;
    const ZIP = 10;
    const COUNTRY = 11;
    const LANGUAGE = 12;

    public function rules(){
        return [
            'date' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'date_of_birth' => 'required',
            'country_of_residence' => 'required'
        ];
    }

    public function messages(){
        return [
            'email.unique' => _lang('Email already exists.'),
        ];
    }

    public function validate($record)
    {
        $rules = $this->rules();
        $messages = $this->messages();
        $validator = Validator::make($record, $rules,$messages);
        return $validator->fails() ? $validator->errors()->all() : [];
    }
}
