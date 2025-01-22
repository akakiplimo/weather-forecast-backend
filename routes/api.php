<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WeatherController;

Route::get('/weather/current', [WeatherController::class, 'current']);
Route::get('/weather/forecast', [WeatherController::class, 'forecast']);