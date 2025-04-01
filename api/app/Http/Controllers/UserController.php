<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers(Request $request, WeatherService $weatherService): JsonResponse
    {
        $limit = $request->query('limit', 20);
        $users = User::inRandomOrder()->take($limit)->get();

        $usersWithWeather = $users->map(function ($user) use ($weatherService) {
            return $weatherService->updateWeatherForUser($user);
        });

        return response()->json($usersWithWeather)
            ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
    }
}
