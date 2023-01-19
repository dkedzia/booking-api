<?php

namespace Tests\Feature\Controllers\v1;

use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class VacancyControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shouldRetrieveVacancies(): void
    {
        // given
        /** @var Vacancy $vacancy */
        $vacancy = Vacancy::factory()->create();

        // when
        $response = $this->makeRequestAsUser('vacancies');

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                $this->getVacancyData($vacancy)
            ]
        ]);
    }

    private function makeRequestAsUser(
        string $endpoint,
        string $method = 'get',
        array  $headers = []): TestResponse
    {
        $user = User::factory()->create();

        return $this->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->{$method}("/api/v1/{$endpoint}", $headers);
    }

    private function getVacancyData(Vacancy $vacancy): array
    {
        return [
            'date' => $vacancy->getDate()->setTime(0, 0)->jsonSerialize(),
            'vacancies_taken' => $vacancy->getVacanciesTaken(),
        ];
    }

    /** @test */
    public function shouldRetrieveVacancy(): void
    {
        // given
        /** @var Vacancy $vacancy */
        $vacancy = Vacancy::factory()->create();
        $date = $vacancy->getDate();

        // when
        $response = $this->makeRequestAsUser("vacancies/show/{$date->format('d-m-Y')}");

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'date' => $date->jsonSerialize(),
            'vacancies_taken' => $vacancy->getVacanciesTaken()
        ]);
    }
}
