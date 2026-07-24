import { Button } from 'antd';
import { useAuthStore } from '@/stores/auth.store';
import { useLogout } from '@/features/auth/hooks/useLogout.ts';

export default function DashboardPage() {
    const user = useAuthStore((state) => state.user);
    const { mutate: logout, isPending } = useLogout();

    return (
        <main className="min-h-screen bg-slate-100 p-6">
            <div className="mx-auto max-w-5xl rounded-xl bg-white p-6 shadow-sm">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-slate-900">Dashboard</h1>

                        <p className="mt-1 text-slate-500">Xin chào, {user?.fullname ?? 'người dùng'}</p>
                    </div>

                    <Button danger loading={isPending} onClick={() => logout()}>
                        Đăng xuất
                    </Button>
                </div>
            </div>
        </main>
    );
}
