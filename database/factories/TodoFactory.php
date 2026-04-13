<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Todo>
 */
class TodoFactory extends Factory
{
    protected $model = Todo::class;

    public function definition(): array
    {
        $hasDueDate = $this->faker->boolean(70);

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->sentence(),
            'due_date' => $hasDueDate ? $this->faker->dateTimeBetween('now', '+7 days')->format('Y-m-d') : null,
            'reminder_time' => $hasDueDate && $this->faker->boolean(60)
                ? $this->faker->time('H:i:s')
                : null,
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'is_completed' => false,
            'completed_at' => null,
        ];
    }
}
