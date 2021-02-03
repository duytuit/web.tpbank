<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Repositories\AuthRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RegisterWithOtpRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\LoginOtpRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\ActivityLogRepository;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangePassOtpRequest;
use Illuminate\Validation\ValidationException;
use App\Repositories\SendOtpRepository;
use App\Models\verify_code_otp;
use GuzzleHttp\Client;
use App\Notifications\SendMailOtp;
use Validator;
use Carbon\Carbon;
use Mail;
use Illuminate\Notifications\Notifiable;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Đăng nhập",
     * description="Login by account, password",
     * operationId="authLogin",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *           required={"account","password"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="password", type="string", format="password"),
     *          ),
     *    ),
     *    @OA\JsonContent(
     *           required={"account","password"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="password", type="string", format="password"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="error")
     *        )
     *     )
     * )
     */
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var AuthRepository
     */
    protected $repo;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'user';
    /**
     * @var SendOtpRepository
     */
    protected $sendotp_repo;
    

    protected $_client;
    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param AuthRepository $repo
     * @param UserRepository $user
     * @param ActivityLogRepository $activity
     */
    public function __construct(
         Request $request,
         AuthRepository $repo,
         UserRepository $user,
         ActivityLogRepository $activity,
         SendOtpRepository $send_otp_repository,
         Client $client
       )
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->user = $user;
        $this->activity = $activity;
        $this->sendotp_repo = $send_otp_repository;
        $this->_client = $client;
    }

    /**
     * Authenticate user
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function authenticate(LoginRequest $request)
    {

        $auth = $this->repo->auth($this->request->all());

        $authUser = $auth['user'];
        $token = $auth['token'];
        $two_factor_code = $auth['two_factor_code'];

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $authUser->id,
            'user_id' => $authUser->id,
            'activity' => 'logged_in'
        ]);

        return $this->success([
            'message' =>  trans('auth.logged_in'),
            'token' => $token,
            'user' => $authUser,
            'two_factor_code' => $two_factor_code
        ]);
    }

    /**
     * Check whether user is authenticated or not
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function check()
    {
        return $this->success($this->repo->check());
    }
    /**
     * Check code user is authenticated or not
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function checkcode(Request $request, $code)
    {
        $user =  $this->user->getByCode($code);

        if(!$user){
            return $this->error(null, ' Thất bại!');
        }
        $result =[
           'id' => $user->id,
           'email' => $user->email,
           'phone' => $user->phone,
           'code' => $user->code,
        ];
        return $this->success([
            'user' => $result,
        ]);
    }

    /**
     * Logout user
     *
     * @return Response|JsonResponse
     */
    public function logout()
    {
        $authUser = Auth::user();

        try {
            $token = JWTAuth::getToken();
        } catch (JWTException $e) {
            return $this->error($e->getMessage());
        }

        JWTAuth::invalidate($token);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $authUser->id,
            'user_id' => $authUser->id,
            'activity' => 'logged_out'
        ]);

        return $this->success(['message' =>  trans('auth.logged_out')]);
    }

    /**
     * Create user
     *
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
     /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Đăng ký",
     * description="register by account, password, password_confirmation",
     * operationId="authRegister",
     * tags={"register"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Đăng ký",
     *    @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *           required={"account","password","password_confirmation"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="password", type="string", format="password"),
     *           @OA\Property(property="password_confirmation", type="string", format="password"),
     *          ),
     *    ),
     *    @OA\JsonContent(
     *           required={"account","password","password_confirmation"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="password", type="string", format="password"),
     *           @OA\Property(property="password_confirmation", type="string", format="password"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="error")
     *        )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {

        $this->repo->validateRegistrationStatus();

        $this->user->create($this->request->all(), 1);

        if(isset($request->account)){
            
            $auth = $this->repo->auth($this->request->all());

            $authUser = $auth['user'];
            $token = $auth['token'];
            $two_factor_code = $auth['two_factor_code'];

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $authUser->id,
                'user_id' => $authUser->id,
                'activity' => 'logged_in'
            ]);

            return $this->success([
                'message' =>  trans('auth.logged_in'),
                'token' => $token,
                'user' => $authUser,
                'two_factor_code' => $two_factor_code
            ]);
        }
        return $this->success(['message' =>  trans('auth.account_created')]);
    }

    /**
     * Activate new user
     *
     * @param $activationToken
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function activate($activationToken)
    {
        $this->repo->activate($activationToken);

        return $this->success(['message' =>  trans('auth.account_activated')]);
    }

    /**
     * Request password reset token for user
     *
     * @param PasswordRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function password(PasswordRequest $request)
    {
        $this->repo->password($this->request->all());

        return $this->success(['message' =>  trans('passwords.sent')]);
    }

    /**
     * Validate user password
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function validatePasswordReset()
    {
        $this->repo->validateResetPasswordToken(request('token'));

        return $this->success(['message' => '']);
    }

    /**
     * Reset user password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function reset(ResetPasswordRequest $request)
    {
        $this->repo->reset($this->request->all());

        return $this->success(['message' =>  trans('passwords.reset')]);
    }

    /**
     * Change user password
     *
     * @param ChangePasswordRequest $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $this->repo->validateCurrentPassword(request('current_password'));

        $this->repo->resetPassword(request('new_password'));

        $this->activity->record([
            'module' => $this->module,
            'module_id' => Auth::user()->id,
            'sub_module' => 'password',
            'activity' => 'resetted'
        ]);

        return $this->success(['message' =>  trans('passwords.change')]);
    }

    /**
     * Verify password during Screen Lock
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function lock(LoginRequest $request)
    {
        $this->repo->validateCurrentPassword(request('password'));

        $this->activity->record([
            'module' => $this->module,
            'module_id' => Auth::user()->id,
            'sub_module' => 'screen',
            'activity' => 'unlocked'
        ]);

        return $this->success(['message' =>  trans('auth.lock_screen_verified')]);
    }
    /**
     * Send - OTP
     *
     * @param SendOtpRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
     /**
     * @OA\Post(
     * path="/api/auth/send-otp",
     * summary="Send OTP",
     * description="Gửi mã OTP",
     * operationId="send_otp",
     * tags={"send-otp"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Gửi mã OTP",
     *    @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *           required={"account"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *          ),
     *    ),
     *    @OA\JsonContent(
     *           required={"account"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="error")
     *        )
     *     )
     * )
     */
    public function SendOtp(SendOtpRequest $request)
    {
        $CodeOTP = $this->getTokenOTP(6);
        if (filter_var($request->account, FILTER_VALIDATE_EMAIL)) {
            try {
                $user = $this->user->findByEmail_v2($request->account);
                if (!$user) {
                    // gửi thông báo tài khoản không tồn tại
                   return $this->error(null,trans('user.could_not_find'));
                } else {
                   $token = $this->repo->passwordApi($this->request->all());
                     $verify_code_otp = verify_code_otp::create([
                          'user_id' => $user->id,
                          'mobile'  => $request->account,
                          'otp_code' => $CodeOTP,
                          'token' => $token,
                          'otp_timeout' => env('TIMER_OUT_VERIFY'),
                          'status' => 1
                         ]);

                     $this->activity->record([
                            'user_id' =>$user->id,
                            'module' => 'verify_code_otp',
                            'module_id' => $verify_code_otp->id,
                            'activity' => 'verify_code_otp'
                     ]);
                     $user->notify(new SendMailOtp($user,$CodeOTP));
                    return $this->success(['message' =>  trans('otp.send_otp_success')]);
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            try {
                $user = $this->user->findByPhone($request->account);
                if (!$user) {
                    // gửi thông báo tài khoản không tồn tại
                     return $this->error(null, trans('user.could_not_find'));
                } else {
                     $token = $this->repo->passwordApi($this->request->all());
                     $verify_code_otp = verify_code_otp::create([
                          'user_id' => $user->id,
                          'mobile'  => $request->account,
                          'otp_code' => $CodeOTP,
                          'token' => $token,
                          'otp_timeout' => env('TIMER_OUT_VERIFY'),
                          'status' => 1
                         ]);

                     $this->activity->record([
                            'user_id' =>$user->id,
                            'module' => 'verify_code_otp',
                            'module_id' => $verify_code_otp->id,
                            'activity' => 'verify_code_otp'
                     ]);
                    $responseReource = $this->_client->request('GET',env('SMS_URL_DXMB').$request->account.'/'.$CodeOTP.'/'.env('SMS_SIGNATURE_DXMB'));
        
                    $result_resource = json_decode((string) $responseReource->getBody(), true);
                    return $this->success(['message' =>  trans('otp.send_otp_success')]);
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }
    /**
     * Register With OTP
     *
     * @param RegisterWithOtpRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
     /**
     * @OA\Post(
     * path="/api/auth/register-otp",
     * summary="Đăng ký với otp",
     * description="register by account, password, password_confirmation",
     * operationId="auth_register_otp",
     * tags={"auth register otp"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Đăng ký với otp",
     *    @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *           required={"account","password","password_confirmation"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="password", type="string", format="password"),
     *           @OA\Property(property="password_confirmation", type="string", format="password"),
     *          ),
     *    ),
     *    @OA\JsonContent(
     *           required={"account","password","password_confirmation"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="password", type="string", format="password"),
     *           @OA\Property(property="password_confirmation", type="string", format="password"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="error")
     *        )
     *     )
     * )
     */
    public function RegisterWithOtp(RegisterWithOtpRequest $request)
    {
        $check_user=$this->user->findByEmailOrPhone($request->account);
        if($check_user){

            return $this->error(null, trans('otp.account_already'));

        }
        $CodeOTP = $this->getTokenOTP(6);
        if (filter_var($request->account, FILTER_VALIDATE_EMAIL)) {
            try {
              
                     $verify_code_otp = verify_code_otp::create([
                          'mobile'  => $request->account,
                          'otp_code' => $CodeOTP,
                          'otp_timeout' => env('TIMER_OUT_VERIFY'),
                          'password' => bcrypt($request->password),
                          'type' => 'register-otp',
                          'status' => 1
                         ]);

                     $this->activity->record([
                            'user_id' => 1,
                            'module' => 'verify_code_otp',
                            'module_id' => $verify_code_otp->id,
                            'activity' => 'verify_code_otp'
                     ]);
                    $subject='Xác Thực Đăng ký Tài Khoản';
                    $content='<h1 style="color: black;">Hello Guest</h1>
                              <p style="color: black;">Verification code registered for your account : '.$CodeOTP.'</p>
                              <p style="color: black;">Thank you!</p>';
                    $cc=$request->account;
                    try {
                        Mail::send([], [], function ($message) use ($cc, $content, $subject) {
                            $message->to($cc)
                                ->subject($subject)
                                ->setBody($content, 'text/html');
                        });
                    } catch (\Exception $e) {
                        return $e->getMessage();
                    }
                    return $this->success(['message' =>  trans('otp.send_otp_success')]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            try {
                     $verify_code_otp = verify_code_otp::create([
                          'mobile'  => $request->account,
                          'otp_code' => $CodeOTP,
                          'otp_timeout' => env('TIMER_OUT_VERIFY'),
                          'password' => bcrypt($request->password),
                          'type' => 'register-otp',
                          'status' => 1
                         ]);

                     $this->activity->record([
                            'user_id' => 1,
                            'module' => 'verify_code_otp',
                            'module_id' => $verify_code_otp->id,
                            'activity' => 'verify_code_otp'
                     ]);
                    $responseReource = $this->_client->request('GET',env('SMS_URL_DXMB').$request->account.'/'.$CodeOTP.'/'.env('SMS_SIGNATURE_DXMB'));
        
                    $result_resource = json_decode((string) $responseReource->getBody(), true);
                    return $this->success(['message' =>  trans('otp.send_otp_success')]);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }
     /**
     * @OA\Post(
     * path="/api/auth/check-register",
     * summary="Kiểm tra đăng ký với otp",
     * description="register by account, verify_code",
     * operationId="register_check",
     * tags={"register check"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Kiểm tra đăng ký với otp",
     *    @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *           required={"account","verify_code"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="verify_code", type="string"),
     *          ),
     *    ),
     *    @OA\JsonContent(
     *           required={"account","verify_code"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="verify_code", type="string"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="error")
     *        )
     *     )
     * )
     */
    public function CheckRegister(LoginOtpRequest $request)
    {
        $gettime = Carbon::now();
        $verifycodeOTP = $this->sendotp_repo->CheckOTPWithAccountAPI($request->account,$request->verify_code);
        if(is_null($verifycodeOTP)){

             return $this->error(null, trans('otp.otp_authentication_failed'));
        }
       $diff = $gettime->getTimestamp() - strtotime($verifycodeOTP->created_at);
        if($diff > env('TIMER_OUT_VERIFY')){

             return $this->error(null, trans('otp.validation_code_expired'));

        }else{
                $this->repo->validateRegistrationStatus();
                $data_user_createOtp=[
                     'account' => $request->account,
                     'password' => $verifycodeOTP->password,
                ];
                $this->user->createOtp($data_user_createOtp, 1);
                return $this->success(['message' =>  trans('auth.account_created')]);
        } 
        return $this->error(null, trans('otp.account_failed'));
    }

     /**
     * login - OTP
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    /**
     * @OA\Post(
     * path="/api/auth/login-otp",
     * summary="Login OTP",
     * description="Login OTP",
     * operationId="login_otp",
     * tags={"login-otp"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Login OTP",
     *    @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *           required={"account","verify_code"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="verify_code", type="string"),
     *          ),
     *    ),
     *    @OA\JsonContent(
     *           required={"account","verify_code"},
     *           @OA\Property(property="account", type="string", example="email/phone"),
     *           @OA\Property(property="verify_code", type="string"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string")
     *        )
     *     )
     * )
     */
    public function LoginOtp(LoginOtpRequest $request)
    {
       $gettime = Carbon::now();
       $verifycodeOTP = $this->sendotp_repo->CheckOTPWithAccountAPI($request->account,$request->verify_code);
        if(is_null($verifycodeOTP)){

            return $this->error(null, trans('otp.otp_authentication_failed'));
        }
       $diff = $gettime->getTimestamp() - strtotime($verifycodeOTP->created_at);
        if($diff > env('TIMER_OUT_VERIFY')){

              return $this->error(null, trans('otp.validation_code_expired'));

        }else{
             $user = $this->user->findById($verifycodeOTP->user_id);
            if( $user ) {
                $access_token = JWTAuth::fromUser($user);
                $responseData = [
                    'message' =>   trans('otp.verification'),
                    'access_token' => $access_token,
                    'token_type' => 'bearer',
                    'user' => $user,
                    'code_change_pass' => $verifycodeOTP->token
                ];
        
                return $this->success($responseData);
            }

            return $this->error(null, trans('otp.account_failed'));

        }
        return $this->error(null, trans('otp.account_failed'));

    }
     /**
     * change pass with otp
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    /**
     * @OA\Post(
     * path="/api/auth/change-pass-otp",
     * summary="Change Pass OTP",
     * description="Change Pass OTP",
     * operationId="change_pass_otp",
     * tags={"change-pass-otp"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Change Pass OTP",
     *    @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *           required={"account","code_change_pass","password","password_confirmation"},
     *           @OA\Property(property="account", type="string"),
     *           @OA\Property(property="code_change_pass", type="string"),
     *           @OA\Property(property="password", type="string", format="password"),
     *           @OA\Property(property="password_confirmation", type="string", format="password"),
     *          ),
     *    ),
     *    @OA\JsonContent(
     *           required={"account","code_change_pass","password","password_confirmation"},
     *           @OA\Property(property="account", type="string"),
     *           @OA\Property(property="code_change_pass", type="string"),
     *           @OA\Property(property="password", type="string", format="password"),
     *           @OA\Property(property="password_confirmation", type="string", format="password"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="error")
     *        )
     *     )
     * )
     */
    public function ChangePassOtp(ChangePassOtpRequest $request)
    {
        if(filter_var($request->account, FILTER_VALIDATE_EMAIL) ) {
            $this->repo->reset([ 'email' => $request->account, 'token' => $request->code_change_pass, 'password' => $request->password]);
            return $this->success([ 'message' =>  trans('otp.password_success')]);
        }else{
            $this->repo->resetAPI(['phone' => $request->account, 'token' => $request->code_change_pass, 'password' => $request->password]);
            return $this->success([ 'message' =>  trans('otp.password_success')]);
        }
    }
    function getTokenOTP($length)
    {
        $token = "";
        $codeAlphabet = "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }
}
