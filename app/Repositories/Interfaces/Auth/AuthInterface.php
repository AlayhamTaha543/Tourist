<?php

namespace App\Repositories\Interfaces\Auth;

use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\OTPRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;

interface AuthInterface
{
    public function login(LoginRequest $request);
    public function signup(RegisterRequest $request);
    public function OTPCode(OTPRequest $request);
    public function resendOTPCode();
    public function logout();
    public function userInfo();
}