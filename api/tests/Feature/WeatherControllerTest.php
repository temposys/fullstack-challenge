<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_weather()
    {
        Bus::fake();

        $user1 = User::factory()->create(['latitude' => 12.34, 'longitude' => 56.78]);
        $user2 = User::factory()->create(['latitude' => 23.45, 'longitude' => 67.89]);

        $this->mock(User::class, function ($mock) use ($user1, $user2) {
            $mock->shouldReceive('findOrFail')
                ->with([$user1->id, $user2->id])
                ->andReturn(collect([$user1, $user2]));
        });

        $response = $this->json('POST', '/weather/update', [
            'users' => [$user1->id, $user2->id],
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }
}
