<?php

namespace Database\Factories;

use App\Models\Vacancy;
use App\Repository\VacancyRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vacancy>
 */
class VacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween()->setTime(0, 0),
            'vacancies_taken' => fake()->numberBetween(0, VacancyRepository::SIMULTANEOUS_VACANCIES)
        ];
    }
}
