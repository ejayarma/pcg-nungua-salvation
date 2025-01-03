<?php

namespace Database\Factories;

use App\Models\ContactPerson;
use App\Models\GenerationalGroup;
use App\Models\Member;
use App\Models\MemberAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->date,
            'generational_group_id' => $this->faker->randomElement(GenerationalGroup::pluck('id')->toArray()),
            'gender' => $this->faker->randomElement(['MALE', 'FEMALE']),
            'is_communicant' => $this->faker->boolean,
            'occupation' => $this->faker->jobTitle,
            'created_at' => $this->faker->randomElement([today()->subDays(2), today()->subDay(), today(),]),
            'updated_at' => $this->faker->randomElement([today()->subDays(2), today()->subDay(), today(),]),
        ];
    }

    /**
     * Configure the factory.
     *
     * @return static
     */
    public function configure()
    {
        return $this->afterCreating(function (Member $member) {
            $member->address()->create(MemberAddress::factory()->make()->toArray());
            $member->contactPerson()->create(ContactPerson::factory()->make()->toArray());
        });
    }
}
