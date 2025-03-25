<?php

namespace App\Http\Controllers;

use App\Jobs\CacheWeatherCommand;
use App\Jobs\UpdateWeatherJob;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WeatherController extends Controller
{
    public function getByUser(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // get weather if it exists in Redis
            $key = "weather:{$user->latitude}:{$user->longitude}";
            $weather = Redis::get($key) ? json_decode(Redis::get($key)) : null;

            // send to update
            dispatch(new UpdateWeatherJob($user));

            return response()->json($weather);
        } catch (ModelNotFoundException $e) {
            abort(404, 'User not found');
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {
            $users = $request->json('users', []);
            $users = User::findOrFail($users);

            $usersWithWeather = $users->map(function ($user) {
                dispatch(new UpdateWeatherJob($user));
                $key = "weather:{$user->latitude}:{$user->longitude}";
                $user->weather = Redis::get($key) ? json_decode(Redis::get($key)) : null;
                return $user;
            });
    
            return response()->json($usersWithWeather);
        } catch (ModelNotFoundException $e) {
            return response()->json([]);
        }
    }
}
