<?php

namespace Tests\Fixtures;

use App\Models\User;

class UserFixture
{
    public static function get(): User
    {
        return User::factory()->create([
            'latitude' => 12.34,
            'longitude' => 56.78,
        ]);
    }

    public static function getSecond(): User
    {
        return User::factory()->create([
            'latitude' => 23.45,
            'longitude' => 67.89
        ]);
    }
}
