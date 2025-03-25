<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WeatherController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    return response()->json([
        'message' => 'all systems are a go',
        'users' => User::all(),
    ]);
});

Route::get('/users', [UserController::class, 'getUsers']);
Route::get('/weather/{userId}', [WeatherController::class, 'getByUser']);
Route::post('/weather/update', [WeatherController::class, 'update']);
