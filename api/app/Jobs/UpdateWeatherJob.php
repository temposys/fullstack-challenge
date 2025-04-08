<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SolgenPower\LaravelOpenWeather\Facades\OpenWeather;

class UpdateWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        try {
            $weather = OpenWeather::coordinates($this->user->latitude, $this->user->longitude);
        } catch (\Throwable $e) {
            Log::error('Weather API error for user [' . $this->user->id . ']: ' . $e->getMessage());
            $weather = null; // API error
        }

        if ($weather) {
            // cache fresh weather data for 1 hour (replace if exists)
            $key = "weather:{$this->user->latitude}:{$this->user->longitude}";
            Cache::set($key, json_encode($weather), 3600);
        }

        // Send fresh weather to frontend if decide to use Websocket
        //  broadcast(new WeatherUpdated($this->user->id, $weather));
    }
}
