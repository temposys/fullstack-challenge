<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateWeatherJob;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WeatherController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        try {
            $usersList = $request->json('users', []);
            $users = User::findOrFail($usersList);

            $usersWithWeather = $users->map(function ($user) {
                dispatch(new UpdateWeatherJob($user));

                $key = "weather:{$user->latitude}:{$user->longitude}";
                $user->weather = Redis::get($key) ? json_decode(Redis::get($key)) : null;
                return $user;
            });
        } catch (ModelNotFoundException $e) {
            $usersWithWeather = [];
        }

        return response()->json($usersWithWeather);
    }
}
