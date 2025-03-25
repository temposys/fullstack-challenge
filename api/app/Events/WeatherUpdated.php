<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use SolgenPower\LaravelOpenWeather\DataTransferObjects\Weather;

class WeatherUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public ?Weather $weather;

    public function __construct(int $userId, ?Weather $weather)
    {
        $this->userId = $userId;
        $this->weather = $weather;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('weather');
    }

    public function broadcastAs(): string
    {
        return 'WeatherUpdated';
    }
}
