<?php

namespace App\Repositories\Impl\Auth;

use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\OTPRequest;
use App\Models\User;
use App\Notifications\OTPNotification;
use App\Repositories\Interfaces\Auth\AuthInterface;
use App\Repositories\Interfaces\ServiceInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authRepository implements AuthInterface
{
    use ApiResponse;
    protected $serviceRepository;
    public function __construct(ServiceInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }
    public function login(LoginRequest $request)
    {
        $request->validated($request->all());

        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            return $this->error('Email not found', 404);
        }

        // Check if password is correct
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Incorrect password', 401);
        }

        $token = $user->createToken('API token for ' . $user->email)->plainTextToken;

        return $this->success('Authenticated', [
            'token' => $token,
        ], 200);
    }

    public function signup(RegisterRequest $request)
    {
        $validatedData = $request->validated(); // No need for $request->all() here

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();
            $imagePath = $request->file('image')->storeAs(
                'profiles',
                $validatedData['first_name'] . '_' . $validatedData['last_name'] . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension(),
                'public'
            );
        }

        // Create user
        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'location' => $validatedData['location'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'image' => $imagePath,
        ]);

        if (!$user) {
            return $this->error('Registration failed', 400);
        }

        // Generate token
        $token = $user->createToken('API Token')->plainTextToken;
        if (!$token) {
            return $this->error('Unable to create token', 400);
        }

        // Send OTP
        $user->generateCode();
        $user->notify(new OTPNotification());

        return $this->success('Registration successful', [
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function OTPCode(OTPRequest $request)
    {
        $request->validated($request->all());

        $user = auth()->user();

        if ($user && $request->code == $user->code && $user->isCodeValid()) {
            $user->resetCode();
            $user->update(['email_verified_at' => now()]);
            return $this->ok('Verified successfully', 200);
        }

        return $this->error('Invalid code', 401);
    }

    public function resendOTPCode()
    {
        $user = auth()->user();

        if ($user && $user->email_verified_at == null) {
            $user->generateCode();
            return $this->ok('OTP code resent successfully', 200);
        }

        return $this->error('You have already verified', 400);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return $this->ok('Logout successful', 200);
    }
    public function userInfo()
    {
        $user = auth()->user();

        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'location' => $user->location,
            'photo_url' => $user->image ? asset('storage/' . $user->image) : null,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'points' => [$this->serviceRepository->UserRank()]
        ];
    }

}