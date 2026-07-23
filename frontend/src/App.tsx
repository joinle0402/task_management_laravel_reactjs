import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import RegisterPage from '@/features/auth/page/RegisterPage.tsx';
import LoginPage from '@/features/auth/page/LoginPage.tsx';
import { GuestRoute } from '@/routes/GuestRoute.tsx';
import DashboardPage from '@/features/dashboard/pages/DashboardPage.tsx';
import { ProtectedRoute } from '@/routes/ProtectedRoute.tsx';

export default function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route element={<GuestRoute />}>
                    <Route path="/register" element={<RegisterPage />} />
                    <Route path="/login" element={<LoginPage />} />
                </Route>

                <Route element={<ProtectedRoute />}>
                    <Route path="/dashboard" element={<DashboardPage />} />
                </Route>

                <Route path="/" element={<Navigate to="/dashboard" replace />} />
                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </BrowserRouter>
    );
}
