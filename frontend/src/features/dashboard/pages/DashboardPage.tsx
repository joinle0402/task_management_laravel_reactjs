import { CheckCircleOutlined, ClockCircleOutlined, ProjectOutlined, UnorderedListOutlined } from '@ant-design/icons';

const statistics = [
    {
        title: 'Tổng công việc',
        value: 24,
        icon: <UnorderedListOutlined />,
        iconClassName: 'bg-blue-100 text-blue-600',
    },
    {
        title: 'Đang thực hiện',
        value: 8,
        icon: <ClockCircleOutlined />,
        iconClassName: 'bg-amber-100 text-amber-600',
    },
    {
        title: 'Đã hoàn thành',
        value: 12,
        icon: <CheckCircleOutlined />,
        iconClassName: 'bg-emerald-100 text-emerald-600',
    },
    {
        title: 'Dự án',
        value: 4,
        icon: <ProjectOutlined />,
        iconClassName: 'bg-violet-100 text-violet-600',
    },
];

export default function DashboardPage() {
    return (
        <div>
            <div className="mb-6">
                <h1 className="text-2xl font-semibold text-slate-800">Tổng quan</h1>

                <p className="mt-1 text-sm text-slate-500">Theo dõi công việc và tiến độ dự án của bạn.</p>
            </div>

            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                {statistics.map((item) => (
                    <div key={item.title} className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-slate-500">{item.title}</p>

                                <p className="mt-2 text-3xl font-semibold text-slate-800">{item.value}</p>
                            </div>

                            <div className={`flex size-11 items-center justify-center rounded-xl text-xl ${item.iconClassName}`}>{item.icon}</div>
                        </div>
                    </div>
                ))}
            </div>

            <div className="mt-6 grid gap-6 xl:grid-cols-3">
                <section className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
                    <h2 className="text-base font-semibold text-slate-800">Công việc gần đây</h2>

                    <div className="mt-4 flex min-h-72 items-center justify-center rounded-lg border border-dashed border-slate-300 text-sm text-slate-400">
                        Task list sẽ đặt tại đây
                    </div>
                </section>

                <section className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 className="text-base font-semibold text-slate-800">Tiến độ</h2>

                    <div className="mt-4 flex min-h-72 items-center justify-center rounded-lg border border-dashed border-slate-300 text-sm text-slate-400">
                        Chart sẽ đặt tại đây
                    </div>
                </section>
            </div>
        </div>
    );
}
