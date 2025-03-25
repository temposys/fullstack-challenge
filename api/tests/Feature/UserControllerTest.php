<?php

namespace Tests\Feature;

use App\Jobs\UpdateWeatherJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_users_with_weather()
    {
        Bus::fake();

        User::factory()->create(['latitude' => 12.34, 'longitude' => 56.78]);

        $response = $this->get('/users?limit=1');

        $response->assertStatus(200);
        $this->assertNull($response->json()[0]['weather']);
        Bus::assertDispatched(UpdateWeatherJob::class);
    }

    public function test_get_users()
    {
        User::factory()->count(5)->create();

        $response = $this->get('/users?limit=3');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }
}
