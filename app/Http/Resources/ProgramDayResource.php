<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgramDayResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'program_id' => $this->program_id,
            'week_number' => $this->week_number,
            'day_of_week' => $this->day_of_week,
            'specific_date' => $this->specific_date,
            'type' => $this->type,
            'workout_id' => $this->workout_id,
            'diet_id' => $this->diet_id,
            'water_goal_ml' => $this->water_goal_ml,
            'steps_goal' => $this->steps_goal,
            
            'workout' => $this->whenLoaded('workout', function() {
                return [
                    'id' => $this->workout->id, 
                    'title' => $this->workout->title,
                    'description' => $this->workout->description,
                    'level_id' => $this->workout->level_id,
                    'workout_type_id' => $this->workout->workout_type_id,
                    
                    // This uses your existing WorkoutDayResource to return the exercises perfectly!
                    'days' => \App\Http\Resources\WorkoutDayResource::collection($this->workout->workoutDay),
                ];
            }),

            'diet' => $this->whenLoaded('diet', function() {
                return [
                    'id' => $this->diet->id, 
                    'title' => $this->diet->title,
                    'calories' => $this->diet->calories,
                    'protein' => $this->diet->protein,
                    'carbs' => $this->diet->carbs,
                    'fat' => $this->diet->fat,
                ];
            }),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}