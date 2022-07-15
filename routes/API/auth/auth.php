<?php

use App\Http\Controllers\API\auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function(){

    // Register User API
    Route::post('register', [RegisterController::class,'register']);


});

