import { CheckSquareOutlined, DashboardOutlined, ProjectOutlined, SettingOutlined } from '@ant-design/icons';
import { Layout, Menu } from 'antd';
import { useLocation, useNavigate } from 'react-router-dom';

const { Sider } = Layout;

interface AppSidebarProps {
    collapsed: boolean;
}

const menuItems = [
    {
        key: '/dashboard',
        icon: <DashboardOutlined />,
        label: 'Tổng quan',
    },
    {
        key: '/tasks',
        icon: <CheckSquareOutlined />,
        label: 'Công việc',
    },
    {
        key: '/projects',
        icon: <ProjectOutlined />,
        label: 'Dự án',
    },
    {
        key: '/settings',
        icon: <SettingOutlined />,
        label: 'Cài đặt',
    },
];

export function AppSidebar({ collapsed }: AppSidebarProps) {
    const navigate = useNavigate();
    const location = useLocation();

    return (
        <Sider
            trigger={null}
            collapsible
            collapsed={collapsed}
            width={240}
            className="app-sidebar fixed bottom-0 left-0 top-0 z-20 min-h-screen overflow-auto"
        >
            <div className={`flex h-16 items-center border-b border-white/10 ${collapsed ? 'justify-center px-0' : 'justify-start px-4'}`}>
                <div className="flex items-center gap-3 overflow-hidden">
                    <div className="app-sidebar-logo flex size-9 shrink-0 items-center justify-center rounded-lg bg-blue-500 text-base font-bold text-white">T</div>

                    {!collapsed && <span className="whitespace-nowrap text-base font-semibold text-white">Task Manager</span>}
                </div>
            </div>

            <Menu
                theme="dark"
                mode="inline"
                selectedKeys={[location.pathname]}
                items={menuItems}
                onClick={({ key }) => navigate(key)}
                className="app-sidebar-menu mt-4"
            />
        </Sider>
    );
}
