<?php

use App\Http\Controllers\v1\ReservationController;
use App\Http\Controllers\v1\VacancyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::middleware('auth.basic')->group(function () {
        Route::apiResource('reservations', ReservationController::class);
        Route::controller(VacancyController::class)
            ->prefix('vacancies')
            ->as('vacancies.')
            ->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('/show/{date}', 'show')->name('show');
            });
    });
});
