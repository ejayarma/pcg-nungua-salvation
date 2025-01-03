<?php

namespace Database\Factories;

use App\Models\MemberAddress;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MemberAddress>
 */
class MemberAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = MemberAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'zip_code' => $this->faker->postcode,
            'street' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'digital_address' => $this->faker->uuid,
            'state_id' => $this->faker->randomElement(State::where('country_id', 84)->pluck('id')->toArray()),
        ];
    }
}
