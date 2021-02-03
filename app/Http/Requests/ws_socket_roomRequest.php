<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ws_socket_roomRequest extends FormRequest
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
                        'Code' => 'data type: string | null',
                        'Hub' => 'data type: string | length: 256 | null',
                        'Name' => 'data type: string | not null',
                        'Users' => 'data type: string | null | comment: các thành viên trong room',
                        'Owner' =>  'data type: string | null | comment: chủ nhân của room',
                    ]);
                }
                break;
            case 'PATCH':
                // request patch
                if(empty($request->all())){
                    throw ValidationException::withMessages([
                        'Code' => 'data type: string | null',
                        'Hub' => 'data type: string | length: 256 | null',
                        'Name' => 'data type: string | not null',
                        'Users' => 'data type: string | null | comment: các thành viên trong room',
                        'Owner' =>  'data type: string | null | comment: chủ nhân của room',
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
            'Name' => 'required'
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
            'Name' => trans('ws_socket_room.name')
        ];
    }
}
