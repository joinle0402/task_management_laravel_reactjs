import { http } from '@/api/http-client.ts';
import { path } from '@/utils/url.util.ts';
import type { AuthResponse, LoginFormValues, RegisterFormValues } from '@/features/auth/types/auth.types.ts';
import type { MessageResponse } from '@/types/api.types.ts';

const rootPath = 'auth';

export const authApi = {
    async login(payload: LoginFormValues) {
        return http.post<AuthResponse>(path(rootPath, 'login'), payload);
    },
    async register(payload: RegisterFormValues) {
        return http.post<AuthResponse>(path(rootPath, 'register'), payload);
    },
    async logout(): Promise<MessageResponse> {
        return http.post(path(rootPath, 'logout'));
    },
};
