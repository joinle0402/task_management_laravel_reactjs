<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:4|confirmed',
        ];
    }

    public function attributes(): array
    {
        return [
            'fullname' => 'Họ tên',
            'username' => 'Tên đăng nhập',
            'password' => 'Mật khẩu',
        ];
    }
}
