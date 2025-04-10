<?php

namespace Tests\Unit;

use App\Jobs\UpdateWeatherJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SolgenPower\LaravelOpenWeather\Facades\OpenWeather;
use Tests\Fixtures\UserFixture;
use Tests\Fixtures\WeatherApiResponseFixture;
use Tests\TestCase;

class UpdateWeatherJobTest extends TestCase
{
    public function test_handle_method_works_correctly()
    {
        $user = UserFixture::get();
        $weatherData = WeatherApiResponseFixture::get();

        OpenWeather::shouldReceive('coordinates')
            ->with($user->latitude, $user->longitude)
            ->once()
            ->andReturn($weatherData);

        $job = new UpdateWeatherJob($user);
        $job->handle();

        // Assert that the weather data was cached correctly
        $cachedWeatherData = Cache::get('weather:'.$user->latitude.':'.$user->longitude);
        $this->assertEquals(json_encode($weatherData), $cachedWeatherData);
    }

    public function test_handle_logs_error_on_api_failure()
    {
        $user = UserFixture::getSecond();

        OpenWeather::shouldReceive('coordinates')
            ->once()
            ->with($user->latitude, $user->longitude)
            ->andThrow(new \Exception('API error'));

        Log::shouldReceive('error')
            ->once()
            ->with('Weather API error for user [' . $user->id . ']: API error');

        $job = new UpdateWeatherJob($user);
        $job->handle();

        $cachedWeatherData = Cache::get('weather:'.$user->latitude.':'.$user->longitude);
        $this->assertNull($cachedWeatherData);
    }
}
