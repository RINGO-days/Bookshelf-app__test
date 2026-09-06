<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;


class LoginController extends Controller 
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email',$request->validated('email'))
            ->first();

        if(!$user || !Hash::check($request->validated('password'),$user->password)){
            return response()->json([
                'error' => 'ログイン情報が正しくありません。'
            ],401);
        }
        $token = $user->createToken('authApiToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
