<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HabitLog>
 */
class HabitLogFactory extends Factory
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
            'habit_id' => Habit::factory()->state(fn (array $attributes) => [
                'user_id' => $attributes['user_id'],
            ]),
            'log_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['completed', 'skipped', 'missed']),
            'qty' => $this->faker->numberBetween(1, 10),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
