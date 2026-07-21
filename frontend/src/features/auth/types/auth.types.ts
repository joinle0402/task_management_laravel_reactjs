import { z } from 'zod';
import { LoginSchema, type RegisterSchema } from '@/features/auth/schemas/auth.schema.ts';

export interface AuthUser {
    id: string;
    fullname: string;
    username: string;
}

export interface AuthResponse {
    token: string;
    user: AuthUser;
}

export type LoginFormValues = z.infer<typeof LoginSchema>;

export type RegisterFormValues = z.infer<typeof RegisterSchema>;
