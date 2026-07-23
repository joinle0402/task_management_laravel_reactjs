import { zodResolver } from '@hookform/resolvers/zod';
import { LoginSchema } from '@/features/auth/schemas/auth.schema.ts';
import type { LoginFormValues } from '@/features/auth/types/auth.types.ts';
import { useForm } from 'react-hook-form';
import { Button, Form } from 'antd';
import { InputField } from '@/components/forms/InputField.tsx';
import { PasswordField } from '@/components/forms/PasswordField.tsx';
import { Link } from 'react-router-dom';
import { useLogin } from '@/features/auth/hooks/useLogin.ts';

export default function LoginPage() {
    const { control, handleSubmit, setError, formState } = useForm<LoginFormValues>({
        resolver: zodResolver(LoginSchema),
        defaultValues: { username: '', password: '' },
    });
    const { isSubmitting } = formState;
    const { mutate: onSubmit, isPending } = useLogin({ setError });

    return (
        <main className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 px-4 py-10">
            <div className="w-full max-w-md rounded-2xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-slate-200/50 sm:p-8">
                <header className="mb-7 text-center">
                    <h1 className="text-2xl font-semibold tracking-tight text-slate-900">Đăng nhập</h1>
                    <p className="mt-2 text-sm leading-6 text-slate-500">Đăng nhập để tiếp tục quản lý công việc</p>
                </header>
                <Form layout="vertical" onFinish={handleSubmit((formValues: LoginFormValues) => onSubmit(formValues))} noValidate>
                    <InputField control={control} name="username" label="Email" placeholder="name@example.com" autoComplete="username" required />
                    <PasswordField
                        control={control}
                        name="password"
                        label="Mật khẩu"
                        placeholder="Nhập mật khẩu"
                        autoComplete="new-password"
                        required
                    />
                    <Button type="primary" htmlType="submit" loading={isPending || isSubmitting} disabled={isPending || isSubmitting} block>
                        Đăng nhập
                    </Button>
                </Form>
                <div className="mt-6 text-center text-sm text-slate-500">
                    Chưa có tài khoản?{' '}
                    <Link to="/register" className="font-medium text-blue-600 hover:text-blue-700">
                        Đăng ký
                    </Link>
                </div>
            </div>
        </main>
    );
}
