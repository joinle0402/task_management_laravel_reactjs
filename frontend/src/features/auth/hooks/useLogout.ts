import { useMutation, useQueryClient } from '@tanstack/react-query';
import { authApi } from '@/features/auth/api/auth.api.ts';
import { useAuthStore } from '@/stores/auth.store.ts';
import { useNavigate } from 'react-router-dom';
import { message } from 'antd';
import type { MessageResponse } from '@/types/api.types.ts';

export function useLogout() {
    const navigate = useNavigate();
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: authApi.logout,
        onSuccess: async (response: MessageResponse) => {
            message.success(response.message);
        },
        onError: async () => {
            message.error('Không thể đăng xuất. Vui lòng thử lại.');
        },
        onSettled: () => {
            useAuthStore.getState().logout();
            queryClient.clear();
            navigate('/login', { replace: true });
        },
    });
}
