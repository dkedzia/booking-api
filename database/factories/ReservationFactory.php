<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_from' => fake()->dateTimeBetween('-1 week')->setTime(0, 0),
            'date_to' => fake()->dateTimeBetween('now', '+1 week')->setTime(0, 0),
        ];
    }
}
