<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Diet;
use App\Models\Advice;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        $diet = Diet::first(); // assuming you already have some diets
        $advice = Advice::first();

        if ($diet && $advice) {
            Program::create([
                'name' => 'Weight Loss Program',
                'price' => 99.99,
                'diet_id' => $diet->id,
                'advice_id' => $advice->id,
                'duration' => '30 days',
                'status' => 1
            ]);
        }
    }
}