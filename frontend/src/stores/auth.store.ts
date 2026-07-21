import type { AuthResponse, AuthUser } from '../features/auth/types/auth.types.ts';
import { persist } from 'zustand/middleware';
import { create } from 'zustand/react';

interface AuthState {
    token?: string | null;
    user?: AuthUser | null;
    isAuthenticated: boolean;
    login: (payload: AuthResponse) => void;
    logout: () => void;
}

export const useAuthStore = create<AuthState>()(
    persist(
        (set) => ({
            token: null,
            user: null,
            isAuthenticated: false,
            login: ({ token, user }) => set({ token, user, isAuthenticated: true }),
            logout: () => set({ token: null, user: null, isAuthenticated: false }),
        }),
        { name: 'auth-storage' },
    ),
);
