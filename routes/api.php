<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TareaController;

Route::get('/tareas', [TareaController::class, 'ListarTodos']);
Route::get('/tareas/{id}', [TareaController::class, 'Buscar']);

Route::middleware(ValidacionToken::class)->group(function () {
    Route::post('/tareas', [TareaController::class, 'Crear']);
    Route::put('/tareas/{id}', [TareaController::class, 'Modificar']);
    Route::delete('/tareas/{id}', [TareaController::class, 'Eliminar']);
    Route::post('/tareas/{id}/comentarios', [TareaController::class, 'AgregarComentario']);
});