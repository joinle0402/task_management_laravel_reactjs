import { useMutation } from '@tanstack/react-query';
import { authApi } from '@/features/auth/api/auth.api.ts';
import type { AuthResponse, RegisterFormValues } from '@/features/auth/types/auth.types.ts';
import type { UseFormSetError } from 'react-hook-form';
import { handleApiError } from '@/utils/handle-errors.util.ts';
import { message } from 'antd';
import { useAuthStore } from '@/stores/auth.store.ts';
import { useNavigate } from 'react-router-dom';

interface UseRegisterOptions {
    setError: UseFormSetError<RegisterFormValues>;
}

export function useRegister({ setError }: UseRegisterOptions) {
    const navigate = useNavigate();
    return useMutation({
        mutationFn: authApi.register,
        onSuccess: async (response: AuthResponse) => {
            useAuthStore.getState().login(response);
            await message.success('Đăng ký thành công.');
            navigate('/dashboard', { replace: true });
        },
        onError: (error: Error) =>
            handleApiError({
                error,
                message,
                setError,
                fallbackMessage: 'Không thể đăng ký tài khoản',
            }),
    });
}
