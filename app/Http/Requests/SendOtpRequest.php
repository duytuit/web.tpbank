<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SendOtpRequest extends FormRequest
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
        if(isset($request->account)){
            if(filter_var($request->account, FILTER_VALIDATE_EMAIL) ) {
                $rules['account'] = 'required|email';
            }else{
                $rules['account'] = 'required|numeric|digits:10|regex:/(0)[0-9]{9}/';
            }
        }else{
            $rules['account'] = 'required';
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
        return [
            'account' => trans('otp.account'),
            'email' => trans('auth.email'),
        ];
    }
}
