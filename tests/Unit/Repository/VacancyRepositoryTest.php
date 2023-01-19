<?php

namespace Tests\Unit\Repository;

use App\Exceptions\VacancyException;
use App\Models\Vacancy;
use App\Repository\VacancyRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacancyRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider getVacanciesData
     */
    public function shouldCheckIfThereAreAnyVacanciesLeftForDatesRange(
        Carbon $dateWithLimitedVacancy,
        int $vacanciesTaken,
        Carbon $dateFrom,
        Carbon $dateTo,
        bool $expectedResult
    ): void
    {
        // given
        Vacancy::create([
            'date' => $dateWithLimitedVacancy,
            'vacancies_taken' => $vacanciesTaken,
        ]);

        // when
        $result = VacancyRepository::checkVacanciesForDatesRange($dateFrom, $dateTo);

        // then
        $this->assertEquals($expectedResult, $result);
    }

    public function getVacanciesData(): array
    {
        return [
            [Carbon::today(), VacancyRepository::SIMULTANEOUS_VACANCIES, Carbon::yesterday(), Carbon::tomorrow(), false],
            [Carbon::today(), VacancyRepository::SIMULTANEOUS_VACANCIES - 1, Carbon::yesterday(), Carbon::tomorrow(), true],
        ];
    }

    /** @test */
    public function shouldIncreaseVacanciesTaken(): void
    {
        // given
        /** @var Vacancy $vacancy */
        $vacancy = Vacancy::create([
            'date' => Carbon::today(),
            'vacancies_taken' => VacancyRepository::SIMULTANEOUS_VACANCIES-1,
        ]);

        // when
        VacancyRepository::increaseTakenVacanciesForDatesRange(Carbon::yesterday()->setTime(0, 0), Carbon::tomorrow()->setTime(0, 0));

        $vacancy->refresh();

        // then
        $this->assertEquals(VacancyRepository::SIMULTANEOUS_VACANCIES, $vacancy->getVacanciesTaken());
    }

    /** @test */
    public function shouldThrowVacancyExceptionWhenTryingToIncreaseMaxVacancyCapacity(): void
    {
        // given
        /** @var Vacancy $vacancy */
        Vacancy::create([
            'date' => Carbon::today(),
            'vacancies_taken' => VacancyRepository::SIMULTANEOUS_VACANCIES,
        ]);

        // then
        $this->expectException(VacancyException::class);

        // when
        VacancyRepository::increaseTakenVacanciesForDatesRange(Carbon::yesterday()->setTime(0, 0), Carbon::tomorrow()->setTime(0, 0));
    }

    /** @test */
    public function shouldDecreaseVacanciesTaken(): void
    {
        // given
        /** @var Vacancy $vacancy */
        $vacancy = Vacancy::create([
            'date' => Carbon::today(),
            'vacancies_taken' => VacancyRepository::SIMULTANEOUS_VACANCIES,
        ]);

        // when
        VacancyRepository::decreaseVacanciesTakenForDatesRange(Carbon::yesterday()->setTime(0, 0), Carbon::tomorrow()->setTime(0, 0));

        $vacancy->refresh();

        // then
        $this->assertEquals(VacancyRepository::SIMULTANEOUS_VACANCIES - 1, $vacancy->getVacanciesTaken());
    }
}
