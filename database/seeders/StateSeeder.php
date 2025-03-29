<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            'Ahafo',
            'Ashanti',
            'Bono',
            'Bono East',
            'Central',
            'Eastern',
            'Greater Accra',
            'North East',
            'Northern',
            'Oti',
            'Savannah',
            'Upper East',
            'Upper West',
            'Volta',
            'Western',
            'Western North'
        ];

        foreach ($regions as $region) {
            State::updateOrCreate(['name' => $region]);
        }
    }
}
