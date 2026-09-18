<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShortUrlFactory extends Factory
{
    public function definition(): array
    {
        return [
            'original_url' => $this->faker->url(),
            'short_code' => Str::random(6),
            'clicks' => 0,
        ];
    }
}
