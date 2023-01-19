<?php

namespace Tests\Feature\Controllers\v1;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Vacancy;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ReservationControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @dataProvider reservationEndpoints
     */
    public function shouldDenyPublicAccess(string $method, string $endpoint): void
    {
        // when
        /** @var TestResponse $response */
        $response = $this->{$method}("/api/v1/{$endpoint}");

        // then
        $response->assertStatus(401);
    }

    public function reservationEndpoints(): array
    {
        return [
            ['get', 'reservations'],
            ['get', 'reservations/1'],
            ['post', 'reservations'],
            ['put', 'reservations/1'],
            ['delete', 'reservations/1'],
        ];
    }

    /** @test */
    public function shouldRetrieveReservations(): void
    {
        // given
        /** @var Reservation $reservation */
        $reservation = Reservation::factory()->create();

        // when
        $response = $this->makeRequestAsUser('reservations');

        // then
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                $this->getReservationData($reservation)
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

    private function getReservationData(Reservation $reservation): array
    {
        return [
            'id' => $reservation->getId(),
            'first_name' => $reservation->getFirstName(),
            'last_name' => $reservation->getLastName(),
            'date_from' => $reservation->getDateFrom()->jsonSerialize(),
            'date_to' => $reservation->getDateTo()->jsonSerialize(),
        ];
    }

    /** @test */
    public function shouldRetrieveReservation(): void
    {
        // given
        /** @var Reservation $reservation */
        $reservation = Reservation::factory()->create();

        // when
        $response = $this->makeRequestAsUser("reservations/{$reservation->getId()}");

        // then
        $response->assertStatus(200);
        $response->assertJson($this->getReservationData($reservation));
    }

    /** @test */
    public function shouldAddReservation(): void
    {
        // given
        $firstName = 'Jan';
        $lastName = 'Kowalski';
        $dateTimeFrom = (new DateTime('-1 week'))->setTime(0, 0);
        $dateTimeTo = (new DateTime())->setTime(0, 0);

        // when
        $response = $this->makeRequestAsUser(
            "reservations",
            'post',
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_from' => Carbon::create($dateTimeFrom)->jsonSerialize(),
                'date_to' => Carbon::create($dateTimeTo)->jsonSerialize(),
            ]
        );

        // then
        $response->assertStatus(201);

        $reservationExists = Reservation::where([
            ['first_name', $firstName],
            ['last_name', $lastName]
        ])->exists();

        $this->assertTrue($reservationExists);
    }

    /** @test */
    public function shouldNotAddReservationIfThereIsNotEnoughVacanciesLeft(): void
    {
        // given
        $firstName = 'Jan';
        $lastName = 'Kowalski';
        $dateTimeFrom = (new DateTime('-1 week'))->setTime(0, 0);
        $dateTimeTo = (new DateTime())->setTime(0, 0);
        Vacancy::create([
            'date' => Carbon::yesterday(),
            'vacancies_taken' => 10
        ]);

        // when
        $response = $this->makeRequestAsUser(
            "reservations",
            'post',
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_from' => Carbon::create($dateTimeFrom)->jsonSerialize(),
                'date_to' => Carbon::create($dateTimeTo)->jsonSerialize(),
            ]
        );

        // then
        $response->assertStatus(400);

        $content = $response->getContent();
        $this->assertEquals('Not enough vacancies', json_decode($content)->message);

        $reservationExists = Reservation::where([
            ['first_name', $firstName],
            ['last_name', $lastName]
        ])->exists();

        $this->assertFalse($reservationExists);
    }

    /** @test */
    public function shouldUpdateReservation(): void
    {
        // given
        $firstName = 'Jan';
        $dateTimeFrom = (new DateTime('-1 week'))->setTime(0, 0);
        $dateTimeTo = (new DateTime())->setTime(0, 0);

        /** @var Reservation $reservation */
        $reservation = Reservation::create([
            'first_name' => $firstName,
            'last_name' => 'Kowalski',
            'date_from' => $dateTimeFrom,
            'date_to' => $dateTimeTo,
        ]);

        // when
        $response = $this->makeRequestAsUser(
            "reservations/{$reservation->getId()}",
            'put',
            [
                'first_name' => $firstName,
                'last_name' => 'Nowak',
                'date_from' => Carbon::create($dateTimeFrom)->jsonSerialize(),
                'date_to' => Carbon::create((new DateTime('+1 week'))->setTime(0, 0))->jsonSerialize(),
            ]
        );

        // then
        $response->assertStatus(200);

        $reservation->refresh();

        $this->assertEquals('Nowak', $reservation->getLastName());
        $this->assertEquals($dateTimeFrom, $reservation->getDateFrom());
        $this->assertEquals($dateTimeTo, $reservation->getDateTo());
    }

    /** @test */
    public function shouldDeleteReservation(): void
    {
        // given
        $dateTimeFrom = (new DateTime('-1 week'))->setTime(0, 0);
        $dateTimeTo = (new DateTime())->setTime(0, 0);

        /** @var Reservation $reservation */
        $reservation = Reservation::create([
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'date_from' => $dateTimeFrom,
            'date_to' => $dateTimeTo,
        ]);

        /** @var Vacancy $vacancy */
        $vacancy = Vacancy::where('date', $dateTimeTo)->firstOrFail();
        $vacancyCounterBeforeTest = $vacancy->getVacanciesTaken();

        // when
        $response = $this->makeRequestAsUser(
            "reservations/{$reservation->getId()}",
            'delete',
            [
                'first_name' => 'Jan',
                'last_name' => 'Nowak',
                'date_from' => Carbon::create($dateTimeFrom)->jsonSerialize(),
                'date_to' => Carbon::create($dateTimeTo)->jsonSerialize(),
            ]
        );

        // then
        $vacancy->refresh();

        $response->assertStatus(200);

        $this->assertFalse($reservation->exists());
        $this->assertEquals($vacancyCounterBeforeTest - 1, $vacancy->getVacanciesTaken());
    }
}
