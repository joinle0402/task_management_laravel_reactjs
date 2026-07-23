import { useAuthStore } from '@/stores/auth.store.ts';
import { Navigate, Outlet } from 'react-router-dom';

export function ProtectedRoute() {
    const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
    if (!isAuthenticated) return <Navigate to="/login" replace />;
    return <Outlet />;
}
