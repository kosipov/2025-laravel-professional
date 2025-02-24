<?php

use Illuminate\Support\Facades\Route;

Route::get('/gagarin-flight', [\App\Http\Controllers\DefaultController::class, 'getGagarinFlight']);
Route::get('flight', [\App\Http\Controllers\DefaultController::class, 'getFlight']);
Route::post('lunar-missions', [\App\Http\Controllers\DefaultController::class, 'addLunarMission']);
Route::get('lunar-missions', [\App\Http\Controllers\DefaultController::class, 'getLunarMissions']);
Route::delete('lunar-missions/{id}', [\App\Http\Controllers\DefaultController::class, 'deleteLunarMission']);
Route::patch('lunar-missions/{id}', [\App\Http\Controllers\DefaultController::class, 'editLunarMission']);
Route::post('space-flights', [\App\Http\Controllers\DefaultController::class, 'addSpaceFlight']);
Route::get('space-flights', [\App\Http\Controllers\DefaultController::class, 'getSpaceFlight']);
Route::get('space-flights', [\App\Http\Controllers\DefaultController::class, 'getSpaceFlight']);
Route::post('book-flight', [\App\Http\Controllers\DefaultController::class, 'bookFlight']);
Route::get('search', [\App\Http\Controllers\DefaultController::class, 'search']);
