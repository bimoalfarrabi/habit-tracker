<?php

namespace Database\Factories;

use App\Models\FocusSession;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FocusSession>
 */
class FocusSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-2 days', 'now');
        $focused = $this->faker->numberBetween(600, 2400);
        $unfocused = $this->faker->numberBetween(0, 600);

        return [
            'user_id' => User::factory(),
            'habit_id' => Habit::factory()->state(fn (array $attributes) => [
                'user_id' => $attributes['user_id'],
            ]),
            'session_date' => $start->format('Y-m-d'),
            'start_time' => $start,
            'end_time' => (clone $start)->modify('+'.($focused + $unfocused).' seconds'),
            'planned_duration_minutes' => $this->faker->randomElement([25, 30, 45, 60]),
            'total_duration_seconds' => $focused + $unfocused,
            'focused_duration_seconds' => $focused,
            'unfocused_duration_seconds' => $unfocused,
            'interruption_count' => $this->faker->numberBetween(0, 5),
            'status' => $this->faker->randomElement(['completed', 'cancelled']),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}
