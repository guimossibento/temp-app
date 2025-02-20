<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CityController;
use App\Http\Controllers\TemperatureController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('dashboard');
});

Route::resource('cities', CityController::class);
//Route::resource('temperatures', TemperatureController::class);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
