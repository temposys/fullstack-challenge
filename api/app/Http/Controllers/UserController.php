<?php

namespace App\Http\Controllers;

use App\Jobs\CacheWeatherCommand;
use App\Jobs\UpdateWeatherJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use SolgenPower\LaravelOpenWeather\Facades\OpenWeather;

class UserController extends Controller
{
    public function getUsers(Request $request): JsonResponse
    {
        $limit = $request->query('limit', 20);
        $users = User::inRandomOrder()->take($limit)->get();

        $usersWithWeather = $users->map(function ($user) {
            // add a queue to update weather
            dispatch(new UpdateWeatherJob($user));
            // get what we have in redis now
            $key = "weather:{$user->latitude}:{$user->longitude}";
            $user->weather = Redis::get($key) ? json_decode(Redis::get($key)) : null;

            return $user;
        });

        return response()->json($usersWithWeather);
    }
}
