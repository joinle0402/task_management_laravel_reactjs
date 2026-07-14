<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class TaskFactory extends Factory
{
    protected $model = Task::class;

    private array $titles = [
        'Triển khai chức năng xác thực người dùng',
        'Sửa lỗi xác thực dữ liệu đăng nhập',
        'Xây dựng API quản lý công việc',
        'Thêm chức năng tìm kiếm và lọc công việc',
        'Triển khai phân trang danh sách công việc',
        'Thiết kế thống kê cho trang tổng quan',
        'Xây dựng trang quản lý người dùng',
        'Tái cấu trúc dịch vụ xác thực',
        'Tối ưu hiệu suất truy vấn công việc',
        'Thêm các quy tắc kiểm tra dữ liệu cho API',
        'Viết unit test cho chức năng xác thực',
        'Triển khai phân quyền theo vai trò',
        'Sửa giao diện responsive trên thiết bị di động',
        'Xây dựng trang chi tiết công việc',
        'Thêm bộ lọc theo mức độ ưu tiên',
        'Triển khai kiểm tra ngày hết hạn công việc',
        'Cập nhật tài liệu API',
        'Sửa lỗi đăng ký trùng email',
        'Thêm trạng thái tải cho danh sách công việc',
        'Triển khai chức năng đăng xuất',
        'Xây dựng các component biểu mẫu có thể tái sử dụng',
        'Tối ưu các chỉ mục trong cơ sở dữ liệu',
        'Thêm danh sách chọn trạng thái công việc',
        'Triển khai chức năng cập nhật thông tin cá nhân',
        'Sửa lỗi trạng thái phân trang',
        'Thêm trạng thái trống cho danh sách công việc',
        'Cải thiện xử lý lỗi',
        'Xây dựng hướng dẫn triển khai lên môi trường production',
        'Thêm thống kê tổng quan công việc trên dashboard',
        'Rà soát và tái cấu trúc module công việc',
    ];

    private array $descriptions = [
        'Triển khai đầy đủ chức năng theo yêu cầu và đảm bảo các trường hợp ngoại lệ được xử lý chính xác.',
        'Rà soát phần triển khai hiện tại, sửa các lỗi đã biết và bổ sung kiểm tra dữ liệu phù hợp.',
        'Hoàn thiện API backend và kiểm tra cấu trúc dữ liệu trả về để đáp ứng yêu cầu của frontend.',
        'Cải thiện phần triển khai hiện tại nhưng vẫn đảm bảo mã nguồn đơn giản và dễ bảo trì.',
        'Bổ sung logic nghiệp vụ, kiểm tra dữ liệu và xử lý lỗi cần thiết cho chức năng này.',
        'Kiểm thử chức năng với nhiều trường hợp khác nhau trước khi đánh dấu công việc là hoàn thành.',
        'Tái cấu trúc các đoạn logic bị trùng lặp và cải thiện khả năng đọc mã mà không thay đổi hành vi hiện tại.',
        'Đồng bộ cấu trúc dữ liệu trả về của API với phần triển khai phía frontend.',
        'Điều tra lỗi được báo cáo và đưa ra giải pháp ổn định, đồng thời thực hiện kiểm thử hồi quy.',
        null,
    ];

    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::cases());
        $priority = fake()->randomElement(TaskPriority::cases());

        return [
            'title' => fake()->randomElement($this->titles),
            'description' => fake()->randomElement($this->descriptions),
            'status' => $status,
            'priority' => $priority,
            'due_date' => $this->generateDueDate($status),
            'created_by' => User::query()->inRandomOrder()->value('id'),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    private function generateDueDate(TaskStatus $status): ?string
    {
        if (fake()->boolean(10)) {
            return null;
        }

        return match ($status) {
            TaskStatus::DONE,
            TaskStatus::CANCELLED => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            TaskStatus::IN_PROGRESS => fake()->dateTimeBetween('-2 weeks', '+1 month')->format('Y-m-d'),
            TaskStatus::TODO => fake()->dateTimeBetween('-1 week', '+3 months')->format('Y-m-d'),
        };
    }
}
