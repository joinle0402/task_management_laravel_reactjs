import { z } from 'zod';

export const LoginSchema = z.object({
    username: z.string().min(1, 'Email không được để trống'),
    password: z.string().min(1, 'Mật khẩu không được để trống'),
});

export const RegisterSchema = z
    .object({
        fullname: z.string().min(1, 'Họ tên không được để trống').max(255, 'Họ tên không được vượt quá 255 ký tự'),
        username: z.string().min(1, 'Tên tài khoản không được để trống').max(255, 'Username không được vượt quá 100 ký tự'),
        password: z.string().min(4, 'Mật khẩu phải có ít nhất 4 ký tự'),
        password_confirmation: z.string().min(1, 'Xác nhận mật khẩu không được để trống'),
    })
    .refine((values) => values.password === values.password_confirmation, {
        message: 'Mật khẩu xác nhận không khớp',
        path: ['password_confirmation'],
    });
