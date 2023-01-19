<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string first_name
 * @property string last_name
 * @property Carbon date_from
 * @property Carbon date_to
 */
class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';
    protected $dates = [
        'date_from',
        'date_to',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'date_from',
        'date_to',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $firstName): static
    {
        $this->first_name = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function setLastName(string $lastName): static
    {
        $this->last_name = $lastName;
        return $this;
    }

    public function getDateFrom(): Carbon
    {
        return $this->date_from;
    }

    public function setDateFrom(string $dateFrom): static
    {
        $this->date_from = $dateFrom;
        return $this;
    }

    public function getDateTo(): Carbon
    {
        return $this->date_to;
    }

    public function setDateTo(string $dateTo): static
    {
        $this->date_to = $dateTo;
        return $this;
    }
}
