<?php

namespace App\Http\Requests\API;

use App\Models\Sclass;
use InfyOm\Generator\Request\APIRequest;

class UpdateSclassAPIRequest extends APIRequest
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
        $rules = Sclass::$rules;
        
        return $rules;
    }
}
