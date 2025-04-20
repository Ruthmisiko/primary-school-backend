<?php

namespace App\Http\Requests\API;

use App\Models\subject;
use InfyOm\Generator\Request\APIRequest;

class UpdatesubjectAPIRequest extends APIRequest
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
        $rules = subject::$rules;
        
        return $rules;
    }
}
