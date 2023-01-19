<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Carbon date
 * @property int vacancies_taken
 */
class Vacancy extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'vacancies';
    protected $dates = [
        'date',
    ];

    protected $fillable = [
        'date',
        'vacancies_taken',
    ];

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function setDate(Carbon $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getVacanciesTaken(): int
    {
        return $this->vacancies_taken;
    }

    public function setVacanciesTaken(int $vacanciesTaken): static
    {
        $this->vacancies_taken = $vacanciesTaken;
        return $this;
    }
}
