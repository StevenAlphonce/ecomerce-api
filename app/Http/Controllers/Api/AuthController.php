<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function register(UserRequest $request)
    {

        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'user created successfully',
            'token' => $token,
            'user' => $user
        ], 201);
    }


    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {

            return response()->json([
                'status' => 'fail',
                'message' => 'Invalid login Credentials'
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'Sucess',
            'token' => $token,
            'user' => $user
        ]);
    }


    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfull'
        ]);
    }
}
