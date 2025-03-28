<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * モデルのデフォルト状態を定義
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'examine_number' => 'E' . fake()->numberBetween(1000, 9999),
            'attendance_number' => 'A' . fake()->numberBetween(100, 999),
            'pass_flag' => fake()->boolean(),
            'attendance_flag' => fake()->boolean(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'created_at' => fake()->dateTimeBetween('-1 year'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at']);
            },
        ];
    }

    /**
     * 合格済みの状態を設定
     */
    public function passed(): static
    {
        return $this->state(fn(array $attributes) => [
            'pass_flag' => true,
            'status' => 'approved',
        ]);
    }

    /**
     * 出席済みの状態を設定
     */
    public function attended(): static
    {
        return $this->state(fn(array $attributes) => [
            'attendance_flag' => true,
        ]);
    }
}