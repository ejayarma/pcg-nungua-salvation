<?php

namespace Database\Seeders;

use App\Models\GenerationalGroup;
use App\Models\Member;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        GenerationalGroup::factory()->count(6)->create();
        GenerationalGroup::query()->find(1)->update(['name' => 'Children Service']);
        GenerationalGroup::query()->find(2)->update(['name' => 'JY']);
        GenerationalGroup::query()->find(3)->update(['name' => 'YPG']);
        GenerationalGroup::query()->find(4)->update(['name' => 'YAF']);
        GenerationalGroup::query()->find(5)->update(['name' => "Women's Fellowship"]);
        GenerationalGroup::query()->find(6)->update(['name' => "Men's Fellowship"]);


        Member::factory(100)->create();
    }
}
