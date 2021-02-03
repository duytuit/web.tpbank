<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserProfilesRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $rules = [];
        if(isset($request->email)){
            $rules['email'] = 'required|email';
        }
        if(isset($request->email)){
            $rules['phone'] = 'required|numeric|digits:10|regex:/(0)[0-9]{9}/';
        }
          $rules['first_name'] = 'sometimes|required';
          $rules['last_name'] = 'sometimes|required';
          $rules['date_of_birth'] = 'date_format:Y-m-d|nullable';
        return  $rules;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'phone' => trans('user.phone'),
            'email' => trans('auth.email'),
            'first_name' => trans('user.first_name'),
            'last_name' => trans('user.last_name'),
            'date_of_birth' => trans('user.date_of_birth'),
        ];
    }
}
