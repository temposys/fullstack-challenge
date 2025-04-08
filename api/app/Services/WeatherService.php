<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Jobs\UpdateWeatherJob;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    public function updateWeatherForUser($users)
    {
        return $users->map(function ($user) {
            dispatch(new UpdateWeatherJob($user));

            $key = "weather:{$user->latitude}:{$user->longitude}";
            $user->weather = Cache::get($key) ? json_decode(Cache::get($key)) : null;
            Log::info('Weather updated for user ' . $user->id . ': ' . json_encode($user->weather));

            return $user;
        });
    }
}
