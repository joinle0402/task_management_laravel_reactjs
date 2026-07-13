<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Http\JsonResponse;

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
        return response()->json(compact('token', 'user'));
    }
}
