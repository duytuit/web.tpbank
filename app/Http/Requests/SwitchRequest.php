<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SwitchRequest extends FormRequest
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
        $method = $request->method();
        switch ($request->method()) {
            case 'POST':
                // request post
                if(empty($request->all())){
                    throw ValidationException::withMessages([
                        'device_id' => 'data type: integer | null',
                        'name' => 'data type: string | length: 256 | not null',
                        'image' => 'data type: text | null',
                        'notify' => 'data type: byte | value: 0 or 1 | value default: 1 | not null | comment: đăng ký nhận thông báo',
                        'interval' =>  'data type: string | length: 191 | null | comment: cài đặt thời gian nhận thông báo',
                        'action' => 'data type: byte | value: 0 or 1 | value default: 1 | not null | comment: thao tác bật - tắt công tắc',
                        'type_id' => 'data type: integer | not null | comment: kiểu hiển công tắc hoặc rèm',
                        'status' => 'data type: byte | value: 0 or 1 | value default: 1 | null',
                    ]);
                }
                break;
            case 'PATCH':
                // request patch
                if(empty($request->all())){
                    throw ValidationException::withMessages([
                        'device_id' => 'data type: integer | null',
                        'name' => 'data type: string | length: 256 | not null',
                        'image' => 'data type: text | null',
                        'notify' => 'data type: byte | value: 0 or 1 | value default: 1 | not null | comment: đăng ký nhận thông báo',
                        'interval' =>  'data type: string | length: 191 | null | comment: cài đặt thời gian nhận thông báo',
                        'action' => 'data type: byte | value: 0 or 1 | value default: 1 | not null | comment: thao tác bật - tắt công tắc',
                        'type_id' => 'data type: integer | not null | comment: kiểu hiển công tắc hoặc rèm',
                        'status' => 'data type: byte | value: 0 or 1 | value default: 1 | null',
                    ]);
                }
                break;
            case 'GET':
                // request get

                break;

            default:
                //  request delete

                break;
        }
        return [
            'name' => 'required'
        ];
    }
     /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('switch.name')
        ];
    }
}
