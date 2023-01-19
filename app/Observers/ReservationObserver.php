<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Repository\VacancyRepository;

class ReservationObserver
{
    public function creating(Reservation $reservation): void
    {
        VacancyRepository::increaseTakenVacanciesForDatesRange($reservation->getDateFrom(), $reservation->getDateTo());
    }

    public function deleted(Reservation $reservation): void
    {
        VacancyRepository::decreaseVacanciesTakenForDatesRange($reservation->getDateFrom(), $reservation->getDateTo());
    }
}
