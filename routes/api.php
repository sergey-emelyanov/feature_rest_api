<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['/api/'], function(){
    Route::get('tasks/', [TaskController::class, 'index']);
    Route::post('tasks/', [TaskController::class, 'store']);
});
