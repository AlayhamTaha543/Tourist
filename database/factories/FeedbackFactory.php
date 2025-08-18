<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeedBack>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'feedback_text' => $this->faker->paragraph,
            'feedback_date' => $this->faker->dateTimeThisYear,
            'feedback_type' => $this->faker->randomElement(['App', 'Taxi', 'Rental', 'Restaurant', 'Hotel', 'Tour', 'Other']),
            'status' => $this->faker->randomElement(['Unread', 'Read', 'Responded']),
            'response_text' => $this->faker->optional()->paragraph,
            'response_date' => $this->faker->optional()->dateTimeThisYear,
        ];
    }
}
