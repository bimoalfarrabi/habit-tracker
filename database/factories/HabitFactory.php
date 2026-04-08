<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Habit>
 */
class HabitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->sentence(),
            'frequency' => $this->faker->randomElement(['daily', 'weekly']),
            'target_count' => $this->faker->numberBetween(1, 8),
            'reminder_time' => $this->faker->optional()->time('H:i:s'),
            'color' => $this->faker->optional()->safeColorName(),
            'icon' => $this->faker->optional()->randomElement(['droplet', 'book-open', 'dumbbell']),
            'is_active' => true,
            'archived_at' => null,
        ];
    }
}
