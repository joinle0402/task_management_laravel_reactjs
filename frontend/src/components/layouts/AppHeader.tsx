import { LoadingOutlined, LogoutOutlined, MenuFoldOutlined, MenuUnfoldOutlined, UserOutlined } from '@ant-design/icons';
import { Avatar, Button, Dropdown, Layout, Space, type MenuProps } from 'antd';

const { Header } = Layout;

interface AppHeaderProps {
    collapsed: boolean;
    onToggleSidebar: () => void;
    onLogout?: () => void;
    logoutLoading?: boolean;
}

export function AppHeader({ collapsed, onToggleSidebar, onLogout, logoutLoading = false }: AppHeaderProps) {
    const userMenuItems: MenuProps['items'] = [
        {
            key: 'profile',
            icon: <UserOutlined />,
            label: 'Thông tin tài khoản',
        },
        {
            type: 'divider',
        },
        {
            key: 'logout',
            icon: logoutLoading ? <LoadingOutlined spin /> : <LogoutOutlined />,
            label: 'Đăng xuất',
            danger: true,
            disabled: logoutLoading,
            onClick: () => {
                if (!logoutLoading) onLogout?.();
            },
        },
    ];

    return (
        <Header className="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-slate-200 !bg-white !px-2 shadow-sm shadow-slate-200/60 sm:!px-4">
            <Button
                type="text"
                icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
                onClick={onToggleSidebar}
                className="flex size-9 items-center justify-center !text-lg !text-slate-700 hover:!bg-slate-100 hover:!text-blue-600"
            />

            <Dropdown menu={{ items: userMenuItems }} trigger={['click']}>
                <button
                    type="button"
                    className="app-header-user flex h-11 cursor-pointer items-center gap-3 rounded-lg px-2 transition hover:bg-slate-100"
                >
                    <Avatar className="shrink-0 !bg-blue-50 !text-blue-600" icon={<UserOutlined />} />

                    <Space vertical size={0} align="start" className="hidden sm:flex">
                        <span className="text-sm font-medium text-slate-700">Nguyễn Văn A</span>

                        <span className="text-xs text-slate-500">Administrator</span>
                    </Space>
                </button>
            </Dropdown>
        </Header>
    );
}
