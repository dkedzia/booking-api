<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationCollection;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/reservations",
     *     tags={"reservation"},
     *     summary="Get all reservations",
     *     operationId="reservationsIndex",
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */
    public function index(): ReservationCollection
    {
        return new ReservationCollection(Reservation::all());
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reservations",
     *     tags={"reservation"},
     *     summary="Add reservation",
     *     operationId="reservationAdd",
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="First name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="string",
     *             example="Jan"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="Last name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="string",
     *             example="Nowak"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Begin date",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="23-01-2023"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="End date",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="30-01-2023"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reservation was added",
     *     )
     * )
     */
    public function store(CreateReservationRequest $reservationRequest): ReservationResource
    {
        try {
            $reservation = new Reservation();
            $reservation->fill($reservationRequest->validated())->save();

            return new ReservationResource($reservation);

        } catch (\Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reservations/{reservation_id}",
     *     tags={"reservation"},
     *     summary="Get reservation",
     *     operationId="reservationsShow",
     *     @OA\Parameter(
     *         name="reservation_id",
     *         in="path",
     *         description="Reservation id",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="string",
     *             example="1"
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="successful operation"
     *     )
     * )
     */
    public function show(int $id): ReservationResource
    {
        $reservation = Reservation::findOrFail($id);
        return new ReservationResource($reservation);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/reservations/{reservation_id}",
     *     tags={"reservation"},
     *     summary="Update reservation",
     *     operationId="reservationsUpdate",
     *     @OA\Parameter(
     *         name="reservation_id",
     *         in="path",
     *         description="Reservation id",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="string",
     *             example="1"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="First name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="string",
     *             example="Jan"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="Last name",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="string",
     *             example="Nowak"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     )
     * )
     */
    public function update(UpdateReservationRequest $updateReservationRequest, int $id): ReservationResource
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->fill($updateReservationRequest->validated())->save();

            return new ReservationResource($reservation);
        } catch (Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/reservations/{reservation_id}",
     *     tags={"reservation"},
     *     summary="Delete reservation",
     *     operationId="reservationsDelete",
     *     @OA\Parameter(
     *         name="reservation_id",
     *         in="path",
     *         description="Reservation id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int",
     *             example=1
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully deleted"
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
    }
}
