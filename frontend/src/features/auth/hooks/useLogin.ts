import { useAuthStore } from '@/stores/auth.store.ts';
import { useMutation } from '@tanstack/react-query';
import { authApi } from '@/features/auth/api/auth.api.ts';
import type { AuthResponse } from '@/features/auth/types/auth.types.ts';

export function useLogin() {
    const login = useAuthStore((state) => state.login);
    return useMutation({
        mutationFn: authApi.login,
        onSuccess: (response: AuthResponse) => {
            login(response);
        },
    });
}
