<?php

namespace Database\Seeders;

use App\Models\CyclingStation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CyclingStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CyclingStation::insert([
            [
                'name' => '1',
                'is_zwift_bike' => true
            ],
            [
                'name' => '2',
                'is_zwift_bike' => true
            ],
            [
                'name' => '3',
                'is_zwift_bike' => true
            ],
            [
                'name' => '4',
                'is_zwift_bike' => false
            ],
        ]);
    }
}
