<?php

namespace App\Repositories;

use App\Models\verify_code_otp;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class SendOtpRepository
{
    /**
     * @var SendOtp
     */
    protected $sendotp;

    /**
     * Instantiate a new controller instance.
     *
     * @param verify_code_otp $verify_code_otp
     */
    public function __construct(verify_code_otp $verify_code_otp)
    {
        $this->sendotp = $verify_code_otp;
    }
     public function CheckOTPWithAccountAPI($account, $verifycode)
    {
        return $this->sendotp->where(['mobile' => $account,'otp_code' => $verifycode])->orderBy('id', 'desc')->first();
    }
}