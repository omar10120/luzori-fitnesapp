<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Advice;
use App\Models\AdviceOption;class AdviceSeeder extends Seeder
{
    public function run()
    {
        $advice = Advice::create([
            'name' => 'Weight Loss Advice',
            'seed_text' => 'Focus on calorie deficit and regular cardio.',
        ]);

        // Attach some options
        $advice->options()->attach([
            AdviceOption::where('key', 'calorie_tracking')->first()->id,
            AdviceOption::where('key', 'meal_planning')->first()->id,
        ]);
    }
}