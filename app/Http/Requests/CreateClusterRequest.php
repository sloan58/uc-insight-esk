<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateClusterRequest extends Request
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
            'name' => 'required|unique:clusters|max:255',
            'ip' => 'required|unique:clusters|ip',
            'user_type' => 'required',
            'version' => 'required',
            'username' => 'required|max:255',
            'password' => 'required|max:255',
        ];
    }
}
