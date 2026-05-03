<?php

namespace Database\Factories;

use app\Enums\SupportLevel;
use app\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'support_level' => null,
        ];
    }

    public function client(): static
    {
        return $this->state([
            'role' => UserRole::CLIENT,
            'support_level' => null,
        ]);
    }

    public function agent(): static
    {

        return $this->state([
            'role' => UserRole::AGENT,
            'support_level' => SupportLevel::N2,
        ]);
    }

    public function admin(): static
    {
        return $this->state([
            'role' => UserRole::ADMIN,
            'support_level' => SupportLevel::N3,
        ]);
    }
}
