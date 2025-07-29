<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ParticipantController;
use App\Http\Controllers\Api\StatsController;


Route::post('/participants', [ParticipantController::class, 'store']);
Route::get('/participants/{id}', [ParticipantController::class, 'show']);
Route::get('/stats', [StatsController::class, 'index']);