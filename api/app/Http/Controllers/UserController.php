<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request, WeatherService $weatherService): JsonResponse
    {
        $limit = $request->query('limit', 20);
        $users = User::inRandomOrder()->take($limit)->get();

        return $this->getUsersWithWeather($users, $weatherService);
    }

    public function update(Request $request, WeatherService $weatherService): JsonResponse
    {
        try {
            $usersList = $request->json('users', []);
            $users = User::findOrFail($usersList);
        } catch (ModelNotFoundException $e) {
            return response()->json([]);
        }

        return $this->getUsersWithWeather($users, $weatherService);
    }

    private function getUsersWithWeather($users, WeatherService $weatherService): JsonResponse
    {
        $usersWithWeather = $weatherService->updateWeatherForUser($users);
        return response()->json($usersWithWeather);
    }
}
