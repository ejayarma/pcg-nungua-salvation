<?php

namespace Database\Seeders;

use App\Models\GenerationalGroup;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_admin' => true,
        ]);

        $this->call(StateSeeder::class);

        GenerationalGroup::count() || GenerationalGroup::factory()->count(6)->create();
        GenerationalGroup::query()->find(1)->update(['name' => 'Children Service', 'description' => 'Children below 12 years of age']);
        GenerationalGroup::query()->find(2)->update(['name' => 'JY', 'description' => 'Teenagers (From 12 to 18 years)']);
        GenerationalGroup::query()->find(3)->update(['name' => 'YPG', 'description' => "Young People's Guild (from 18 to 29 years)"]);
        GenerationalGroup::query()->find(4)->update(['name' => 'YAF', 'description' => 'Young Adults Fellowship (From 30 to 39 years)']);
        GenerationalGroup::query()->find(5)->update(['name' => "Women's Fellowship", 'description' => 'Women aged above 40']);
        GenerationalGroup::query()->find(6)->update(['name' => "Men's Fellowship", 'description' => 'Men aged above 40']);

        Member::factory(100)->create();
    }
}
