<?php

use App\Http\Controllers\Api\CountryCitiesController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\TemperatureController;

Route::apiResource('temperatures', TemperatureController::class);
Route::get('/countries', [CountryCitiesController::class, 'countries']);
Route::get('/countries/{country}/cities', [CountryCitiesController::class, 'cities']);
