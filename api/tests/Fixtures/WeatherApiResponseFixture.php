<?php

namespace Tests\Fixtures;

use Illuminate\Support\Carbon;
use SolgenPower\LaravelOpenWeather\DataTransferObjects\Weather;

class WeatherApiResponseFixture
{
    public static function get(): Weather
    {
        return new Weather(
            latitude: 12.34,
            longitude: 56.78,
            countryCode: null,
            city: '',
            condition: 'Clear',
            description: 'clear sky',
            icon: 'https://openweathermap.org/img/wn/01n.png',
            temperature: 80.08,
            feelsLike: 83.46,
            pressure: 1014,
            humidity: 73,
            windSpeed: 14.18,
            windAngle: 24,
            windDirection: 'NNE',
            cloudiness: 6,
            visibility: 10000,
            timezone: 14400,
            sunrise: new Carbon('2025-03-25T02:13:48.000000Z'),
            sunset: new Carbon('2025-03-25T14:23:48.000000Z'),
            calculatedAt: new Carbon('2025-04-07T02:26:23.000000Z'),
        );
    }
}
