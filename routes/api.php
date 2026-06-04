<?php

use App\Http\Controllers\Api\CareRequestApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas de la API para la aplicación GoPET.
| Estas rutas se cargan automáticamente bajo el prefijo "/api" gracias
| a la configuración del bootstrap del framework.
|
*/

Route::get('/care-requests', [CareRequestApiController::class, 'index'])->name('api.care-requests');
Route::get('/dogs', [CareRequestApiController::class, 'dogs'])->name('api.dogs');
