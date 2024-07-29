<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\MetroController;


Route::get('/v1/cheapest-route', [MetroController::class, 'getCheapestRoute']);
