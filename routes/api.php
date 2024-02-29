<?php

use App\Http\Controllers\API\BuildingController;
use App\Http\Controllers\API\BuildingOwnerController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::get('unauthenticated', function () {
    return response()->json(['error' => 'Unauthenticated'], 401);
})->name('unauthenticated');


Route::post('login', [RegisterController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:api')->group( function () {
    Route::get('/buildings/list', [BuildingController::class, 'getListWithFilters']);
    Route::patch('/building-owner/{id}/disable', [BuildingOwnerController::class, 'disable']);
});
