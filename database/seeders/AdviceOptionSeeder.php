<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdviceOption;

class AdviceOptionSeeder extends Seeder
{
    public function run()
    {
        $options = [
            ['key' => 'calorie_tracking', 'label' => 'Calorie Tracking', 'order' => 1],
            ['key' => 'meal_planning', 'label' => 'Meal Planning', 'order' => 2],
            ['key' => 'exercise_routine', 'label' => 'Exercise Routine', 'order' => 3],
            ['key' => 'protein_intake', 'label' => 'Protein Intake', 'order' => 4],
            ['key' => 'strength_training', 'label' => 'Strength Training', 'order' => 5],
        ];

        foreach ($options as $opt) {
            AdviceOption::create($opt);
        }
    }
}