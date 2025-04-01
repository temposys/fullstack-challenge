<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function update(Request $request, WeatherService $weatherService): JsonResponse
    {
        try {
            $usersList = $request->json('users', []);
            $users = User::findOrFail($usersList);

            $usersWithWeather = $users->map(function ($user) use ($weatherService) {
                return $weatherService->updateWeatherForUser($user);
            });
        } catch (ModelNotFoundException $e) {
            $usersWithWeather = [];
        }

        return response()->json($usersWithWeather);
    }
}
