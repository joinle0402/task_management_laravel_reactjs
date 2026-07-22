import { zodResolver } from '@hookform/resolvers/zod';
import { RegisterSchema } from '@/features/auth/schemas/auth.schema.ts';
import type { RegisterFormValues } from '@/features/auth/types/auth.types.ts';
import { useForm } from 'react-hook-form';
import { Button, Form } from 'antd';
import { InputField } from '@/components/forms/InputField.tsx';
import { PasswordField } from '@/components/forms/PasswordField.tsx';
import { Link } from 'react-router-dom';

export default function RegisterPage() {
    const { control, handleSubmit } = useForm<RegisterFormValues>({
        resolver: zodResolver(RegisterSchema),
        defaultValues: {
            fullname: '',
            username: '',
            password: '',
            password_confirmation: '',
        },
    });

    const onSubmit = (formValues: RegisterFormValues) => {
        console.log(formValues);
    };

    return (
        <main className="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10">
            <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm sm:p-8">
                <div className="mb-4 text-center">
                    <h1 className="text-2xl font-semibold text-slate-900">Tạo tài khoản</h1>
                    <p className="mt-2 text-sm text-slate-500">Đăng ký để bắt đầu quản lý công việc</p>
                </div>
                <Form layout="vertical" onFinish={handleSubmit(onSubmit)} noValidate>
                    <InputField control={control} name="fullname" label="Họ và tên:" placeholder="John Smith" required />
                    <InputField control={control} name="username" label="Tên đăng nhập:" placeholder="johnsmith2001it@gmail.com" required />
                    <PasswordField control={control} name="password" label="Mật khẩu:" placeholder="Nhập mật khẩu" required />
                    <PasswordField
                        control={control}
                        name="password_confirmation"
                        label="Xác nhận mật khẩu:"
                        placeholder="Nhập lại mật khẩu"
                        required
                    />
                    <Button type="primary" htmlType="submit" block>
                        Đăng ký
                    </Button>
                </Form>
                <div className="mt-6 text-center text-sm text-slate-500">
                    Đã có tài khoản?{' '}
                    <Link to="/login" className="font-medium text-blue-600 hover:text-blue-700">
                        Đăng nhập
                    </Link>
                </div>
            </div>
        </main>
    );
}
