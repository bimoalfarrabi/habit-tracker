<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserNotification>
 */
class UserNotificationFactory extends Factory
{
    protected $model = UserNotification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['habit_reminder', 'daily_summary']),
            'title' => $this->faker->sentence(3),
            'message' => $this->faker->sentence(),
            'data' => [
                'habit_id' => $this->faker->numberBetween(1, 100),
                'habit_title' => $this->faker->words(2, true),
            ],
            'is_read' => false,
            'read_at' => null,
            'scheduled_for' => now(),
        ];
    }
}
