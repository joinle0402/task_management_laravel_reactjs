import { z } from 'zod';

export const LoginSchema = z.object({
    username: z.string().min(1, 'Vui lòng nhập username'),
    password: z.string().min(1, 'Vui lòng nhập mật khẩu'),
});

export const RegisterSchema = z
    .object({
        fullname: z.string().min(1, 'Vui lòng nhập họ tên').max(255, 'Họ tên không được vượt quá 255 ký tự'),
        username: z.string().min(1, 'Vui lòng nhập username').max(100, 'Username không được vượt quá 100 ký tự'),
        password: z.string().min(4, 'Mật khẩu phải có ít nhất 4 ký tự'),
        password_confirmation: z.string().min(1, 'Vui lòng xác nhận mật khẩu'),
    })
    .refine((values) => values.password === values.password_confirmation, {
        message: 'Mật khẩu xác nhận không khớp',
        path: ['passwordConfirmation'],
    });
