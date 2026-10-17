<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;


Route::group(['/api/'], function(){
    Route::get('tasks/', [TaskController::class, 'index']);
    Route::post('tasks/', [TaskController::class, 'store']);
    Route::get('tasks/{id}/', [TaskController::class, 'show']);
    Route::put('tasks/{id}', [TaskController::class, 'update']);
});
