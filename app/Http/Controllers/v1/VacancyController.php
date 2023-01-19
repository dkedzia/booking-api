<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VacancyCollection;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;
use Carbon\Carbon;

class VacancyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/vacancies",
     *     tags={"vacancy"},
     *     summary="Get all vacancies",
     *     operationId="vacanciesIndex",
     *     @OA\Response(
     *         response=200,
     *         description="Retrived successfully"
     *     )
     * )
     */
    public function index(): VacancyCollection
    {
        return new VacancyCollection(Vacancy::all());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/vacancies/show/{date}",
     *     tags={"vacancy"},
     *     summary="Get one vacancy",
     *     operationId="vacancyShow",
     *     @OA\Parameter(
     *         name="date",
     *         in="path",
     *         description="Vacancy taken date",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="string",
     *             example="20-01-2023"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retrived successfully"
     *     )
     * )
     */
    public function show(string $date): VacancyResource
    {
        $dateFromString = Carbon::parse($date);
        $vacancy = Vacancy::where('date', $dateFromString)->first();
        return new VacancyResource($vacancy);
    }
}
