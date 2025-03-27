<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
        return [
            'name' => fake()->name(),

            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'mail' => fake()->unique()->safeEmail(),
            'name_kana' => fake()->randomElement(),
            'gender' => fake()->numberBetween(1, 2), // 1: 男性, 2: 女性
            'birthday' => fake()->date(),
            'work_name' => fake()->company(),
            'work_zipcode' => fake()->postcode(),
            'work_prefecture' => fake()->numberBetween(1, 47),
            'work_address1' => fake()->city(),
            'work_address2' => fake()->streetAddress(),
            'work_building' => fake()->secondaryAddress(),
            'work_section' => fake()->randomElement(['内科', '外科', '小児科', '産婦人科']),
            'work_phone' => fake()->phoneNumber(),
            'send_flag' => fake()->boolean(),
            'zipcode' => fake()->postcode(),
            'prefecture' => fake()->numberBetween(1, 47),
            'address1' => fake()->city(),
            'address2' => fake()->streetAddress(),
            'building' => fake()->secondaryAddress(),
            'status' => fake()->numberBetween(0, 3),
            'certification_number' => fake()->numerify('########'),
            'certification_date' => fake()->date(),
            'expired_date' => fake()->date(),
            'exp_edu' => fake()->numberBetween(0, 1),
            'temp_mail' => fake()->unique()->safeEmail(),
            'certification_text' => fake()->sentence(),
            'password_updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'deleted_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
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