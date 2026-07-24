import { useState } from 'react';
import { Layout } from 'antd';
import { Outlet } from 'react-router-dom';
import { AppHeader } from '@/components/layouts/AppHeader.tsx';
import { AppSidebar } from '@/components/layouts/AppSidebar.tsx';
import { useLogout } from '@/features/auth/hooks/useLogout.ts';

const { Content } = Layout;

export default function MainLayout() {
    const [collapsed, setCollapsed] = useState(false);
    const { mutate: logout, isPending } = useLogout();

    const handleLogout = () => logout();

    return (
        <Layout className="min-h-screen">
            <AppSidebar collapsed={collapsed} />

            <Layout className="min-h-screen">
                <AppHeader
                    collapsed={collapsed}
                    onToggleSidebar={() => setCollapsed((current) => !current)}
                    onLogout={handleLogout}
                    logoutLoading={isPending}
                />

                <Content className="!bg-slate-100 !p-4 !sm:p-6">
                    <div className="min-h-[calc(100vh-112px)]">
                        <Outlet />
                    </div>
                </Content>
            </Layout>
        </Layout>
    );
}
