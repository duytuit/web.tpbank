<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class LoginOtpRequest extends FormRequest
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
        $rules = [
            'verify_code' => 'required|max:6',
        ];
        if(isset($request->account)){
            if(filter_var($request->account, FILTER_VALIDATE_EMAIL) ) {
                $rules['account'] = 'required|email';
            }else{
                $rules['account'] = 'required|numeric|digits:10|regex:/(0)[0-9]{9}/';
            }
        }else{
            $rules['account'] = 'required|email';
        }
        return $rules;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = [
            'account' => trans('otp.account'),
            'verify_code' => trans('auth.verify_code'),
        ];

        return $attributes;
    }
}
