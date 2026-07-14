<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lastName = fake()->randomElement([
            'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng',
            'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng',
            'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương',
        ]);

        $middleName = fake()->randomElement([
            'Văn', 'Thị', 'Hoàng', 'Minh',
            'Ngọc', 'Thanh', 'Đức', 'Quốc',
        ]);

        $firstName = fake()->randomElement([
            'An', 'Anh', 'Bảo', 'Duy', 'Hà',
            'Hải', 'Hiếu', 'Hùng', 'Khánh', 'Linh',
            'Long', 'Mai', 'Minh', 'Nam', 'Ngân',
            'Phong', 'Phúc', 'Quân', 'Trang', 'Tuấn',
        ]);

        $fullname = "{$lastName} {$middleName} {$firstName}";
        $username = Str::slug($fullname, '').fake()->unique()->numberBetween(1, 9999).'@gmail.com';

        return [
            'fullname' => $fullname,
            'username' => $username,
            'password' => bcrypt('1106'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
