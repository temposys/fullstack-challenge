<?php

namespace Tests\Feature;

use App\Jobs\UpdateWeatherJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\Fixtures\UserFixture;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_users_with_weather()
    {
        Bus::fake();

        UserFixture::get();

        $response = $this->get('/users?limit=1');

        $response->assertStatus(200);
        $this->assertNull($response->json()[0]['weather']);
        Bus::assertDispatched(UpdateWeatherJob::class);
    }

    public function test_get_users()
    {
        Bus::fake();

        User::factory()->count(5)->create();

        $response = $this->get('/users?limit=3');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    public function test_update_weather()
    {
        Bus::fake();

        $user1 = UserFixture::get();
        $user2 = UserFixture::getSecond();

        $this->mock(User::class, function ($mock) use ($user1, $user2) {
            $mock->shouldReceive('findOrFail')
                ->with([$user1->id, $user2->id])
                ->andReturn(collect([$user1, $user2]));
        });

        $response = $this->json('POST', '/users', [
            'users' => [$user1->id, $user2->id],
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }
}
