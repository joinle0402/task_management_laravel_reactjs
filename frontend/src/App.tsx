import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import RegisterPage from '@/features/auth/page/RegisterPage.tsx';
import LoginPage from '@/features/auth/page/LoginPage.tsx';

export default function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/register" element={<RegisterPage />} />
                <Route path="/login" element={<LoginPage />} />
                <Route path="/" element={<Navigate to="/register" replace />} />
                <Route path="*" element={<Navigate to="/register" replace />} />
            </Routes>
        </BrowserRouter>
    );
}
