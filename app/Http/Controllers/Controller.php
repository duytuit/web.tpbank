<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
/**
 * @OA\Info(
 *    title="API Vay24hTPbank",
 *    version="1.0.0",
 * )
 * @SWG\Swagger(
 *      schemes={"http", "https"},
 *      @SWG\Info(
 *          version="1.0.0",
 *          title="L5 Swagger API",
 *          description="L5 Swagger API description",
 *          @SWG\Contact(
 *              email="darius@matulionis.lt"
 *          ),
 *      )
 *  )
 */
/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * Return success response
     *
     * @param null $items
     * @param int $status
     *
     * @return JsonResponse
     */
    public function success($items = null, $status = 200)
    {
        $data = ['success' => true];
        if ($items instanceof Arrayable) {
            $items = $items->toArray();
        }
        if ($items) {
            foreach ($items as $key => $item) {
                if($key == 'message'){
                   $data[$key] = (array)$item;
                }else{
                   $data[$key] = $item;
                }
              
            }
        }

        return response()->json($data, $status);
    }

    /**
     * Used to return error response
     *
     * @param null $items
     * @param int $status
     *
     * @return JsonResponse
     */
    public function error($items = null, $message = null, $status = 422)
    {

        $data = ['success' => false,'message' => (array)$message];
        if ($items) {
            foreach ($items as $key => $item) {
                $data['errors'][$key][] = $item;
            }
        }

        return response()->json($data, $status);
    }
}
