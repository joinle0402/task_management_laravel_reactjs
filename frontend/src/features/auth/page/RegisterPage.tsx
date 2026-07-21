import { zodResolver } from '@hookform/resolvers/zod';
import { RegisterSchema } from '@/features/auth/schemas/auth.schema.ts';
import type { RegisterFormValues } from '@/features/auth/types/auth.types.ts';
import { useForm } from 'react-hook-form';
import { FormInput } from '@/components/forms/FormInput.tsx';
import { Form } from 'antd';

export function RegisterPage() {
    const { control, handleSubmit, formState } = useForm<RegisterFormValues>({
        resolver: zodResolver(RegisterSchema),
        defaultValues: {
            username: '',
            password: '',
        },
    });

    const onSubmit = (formValues: RegisterFormValues) => {
        console.log(formValues);
    };

    return (
        <main className="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10">
            <div className="w-full max-w-md rounded-2xl bg-white p-6 shadow-sm sm:p-8">
                <Form layout="vertical" onFinish={handleSubmit(onSubmit)} noValidate>
                    <FormInput control={control} name="username" label="Tên đăng nhập:" placeholder="johnsmith2001it@gmail.com" required />
                    <FormInput control={control} name="password" label="Mật khẩu:" placeholder="johnsmith2001it@gmail.com" required />
                </Form>
            </div>
        </main>
    );
}
