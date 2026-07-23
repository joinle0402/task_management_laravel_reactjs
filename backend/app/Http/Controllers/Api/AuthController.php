<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    #[BodyParameter('fullname', description: 'Họ và tên', example: 'John Smith')]
    #[BodyParameter('username', description: 'Tên đăng nhập', example: 'johnsmith2001it@gmail.com')]
    #[BodyParameter('password', description: 'Mật khẩu', example: '1106')]
    #[BodyParameter('password_confirmation', description: 'Nhập lại mật khẩu', example: '1106')]
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $token = $user->createToken('access-token')->plainTextToken;
        return response()->json(compact('token', 'user'), 201);
    }

    #[BodyParameter('username', description: 'Tên đăng nhập', example: 'johnsmith2001it@gmail.com')]
    #[BodyParameter('password', description: 'Mật khẩu', example: '1106')]
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('username', $request->username)->first();
        throwIf(empty($user) || Hash::check($request->password, $user->password) == false, 'Tên tài khoản hoặc mật khẩu không chính xác.', 401);
        $token = $user->createToken('access-token')->plainTextToken;
        return response()->json(compact('token', 'user'));
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Đăng xuất thành công.']);
    }
}
