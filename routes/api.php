<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['/api/tasks/'], function(){
    Route::get('/', [TaskController::class, 'index']);
});
