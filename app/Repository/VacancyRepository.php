<?php

namespace App\Repository;

use App\Exceptions\VacancyException;
use App\Models\Vacancy;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VacancyRepository
{
    public const SIMULTANEOUS_VACANCIES = 10;

    /** @throws VacancyException */
    public static function increaseTakenVacanciesForDatesRange(Carbon $dateFrom, Carbon $dateTo): void
    {
        if (!static::checkVacanciesForDatesRange($dateFrom, $dateTo)) {
            throw new VacancyException('Not enough vacancies');
        }
        DB::transaction(function () use ($dateFrom, $dateTo) {
            foreach (static::getAllDatesInRange($dateFrom, $dateTo) as $date) {
                /** @var Builder $vacancyBuilder */
                $vacancyBuilder = Vacancy::where('date', $date);
                if (!$vacancyBuilder->exists()) {
                    $vacancy = new Vacancy();
                    $vacancy->setVacanciesTaken(1)
                        ->setDate($date)
                        ->save();
                } else {
                    $vacancies = $vacancyBuilder->get();
                    /** @var Vacancy $vacancy */
                    foreach ($vacancies as $vacancy) {
                        $vacancy->setVacanciesTaken($vacancy->getVacanciesTaken() + 1);
                        $vacancy->save();
                    }
                }
            }
        });

    }

    public static function checkVacanciesForDatesRange(Carbon $dateFrom, Carbon $dateTo): bool
    {
        $vacanciesCount = Vacancy::where([
            ['date', '>=', $dateFrom],
            ['date', '<=', $dateTo],
            ['vacancies_taken', '=', self::SIMULTANEOUS_VACANCIES]
        ])->count();
        return !$vacanciesCount;
    }

    /** @return Carbon[] */
    private static function getAllDatesInRange(Carbon $dateFrom, Carbon $dateTo): array
    {
        $dateRange = CarbonPeriod::create($dateFrom, $dateTo);
        return $dateRange->toArray();
    }

    public static function decreaseVacanciesTakenForDatesRange(Carbon $dateFrom, Carbon $dateTo): void
    {
        $vacancies = Vacancy::where([
            ['date', '>=', $dateFrom],
            ['date', '<=', $dateTo],
        ])->get();

        /** @var Vacancy $vacancy */
        foreach ($vacancies as $vacancy) {
            $vacanciesTaken = $vacancy->getVacanciesTaken();
            if ($vacanciesTaken < 1) {
                Log::alert(
                    "Not enough Vacancies taken to decrease the amount for date {$vacancy->getDate()->toAtomString()}"
                );
                $vacancy->setVacanciesTaken(0);
            }
            $vacancy->setVacanciesTaken($vacanciesTaken - 1);
            $vacancy->save();
        }
    }
}
