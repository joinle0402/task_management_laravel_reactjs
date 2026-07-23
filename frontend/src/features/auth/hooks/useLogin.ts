import { useAuthStore } from '@/stores/auth.store.ts';
import { useMutation } from '@tanstack/react-query';
import { authApi } from '@/features/auth/api/auth.api.ts';
import type { AuthResponse, LoginFormValues } from '@/features/auth/types/auth.types.ts';
import type { UseFormSetError } from 'react-hook-form';
import { handleApiError } from '@/utils/handle-errors.util.ts';
import { message } from 'antd';
import { useNavigate } from 'react-router-dom';

interface UseLoginOptions {
    setError: UseFormSetError<LoginFormValues>;
}

export function useLogin({ setError }: UseLoginOptions) {
    const navigate = useNavigate();
    const login = useAuthStore((state) => state.login);
    return useMutation({
        mutationFn: authApi.login,
        onSuccess: async (response: AuthResponse) => {
            login(response);
            message.success('Đăng nhập thành công.');
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
