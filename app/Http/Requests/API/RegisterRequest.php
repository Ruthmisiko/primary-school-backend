<?php

namespace App\Http\Requests\API;

use InfyOm\Generator\Request\APIRequest;

class RegisterRequest extends APIRequest
{
    public $validator = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    protected function failedValidation($validator)
    {
        $this->validator = $validator;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'                  => 'required',
            'email'                 => 'email|required|unique:users,email,NULL,id',
            'password'              => 'required|min:4|confirmed',
            'password_confirmation' => 'required_with:password'
        ];

        return $rules;
    }
}
