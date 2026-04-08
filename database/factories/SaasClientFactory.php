<?php

namespace Database\Factories;

use App\Models\SaasClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SaasClientFactory extends Factory
{
    protected $model = SaasClient::class;

    public function definition(): array
    {
        $name = $this->faker->company();
        return [
            'id'         => Str::uuid(),
            'name'       => $name,
            'slug'       => Str::slug($name) . '-' . Str::random(4),
            'email'      => $this->faker->companyEmail(),
            'phone'      => $this->faker->phoneNumber(),
            'status'     => 1,
            'is_suspended' => false,
        ];
    }
}