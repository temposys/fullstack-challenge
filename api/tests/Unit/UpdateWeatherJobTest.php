<?php

namespace Tests\Unit;

use App\Jobs\UpdateWeatherJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use SolgenPower\LaravelOpenWeather\Facades\OpenWeather;
use Tests\Fixtures\WeatherApiResponseFixture;
use Tests\TestCase;

class UpdateWeatherJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_updates_weather()
    {
        $user = User::factory()->create(['latitude' => 12.34, 'longitude' => 56.78]);

        OpenWeather::shouldReceive('coordinates')
            ->once()
            ->with($user->latitude, $user->longitude)
            ->andReturn(WeatherApiResponseFixture::get());

        $job = new UpdateWeatherJob($user);
        $job->handle();

        $this->assertEquals(
            json_encode(WeatherApiResponseFixture::get()),
            Redis::get("weather:{$user->latitude}:{$user->longitude}")
        );
    }

    public function test_handle_logs_error_on_api_failure()
    {
        $user = User::factory()->create(['latitude' => 12.34, 'longitude' => 56.78]);

        OpenWeather::shouldReceive('coordinates')
            ->once()
            ->with($user->latitude, $user->longitude)
            ->andThrow(new \Exception('API error'));

        Log::shouldReceive('error')
            ->once()
            ->with('Weather API error: API error');

        $job = new UpdateWeatherJob($user);
        $job->handle();
    }
}
