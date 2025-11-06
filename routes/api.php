<?php

use App\Http\Controllers\EventoController;
use Illuminate\Support\Facades\Route;

// Todas as rotas de eventos requerem autenticação por token
Route::middleware('auth.token')->group(function () {
    Route::apiResource('eventos', EventoController::class);
});

