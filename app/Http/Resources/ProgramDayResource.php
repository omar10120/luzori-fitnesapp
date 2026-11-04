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
            
            // ADD THESE BLOCKS:
            'workout' => $this->whenLoaded('workout', function() {
                return [
                    'id' => $this->workout->id, 
                    'title' => $this->workout->title,
                    // Add any other fields the app needs (e.g., image, level_id)
                ];
            }),
            'diet' => $this->whenLoaded('diet', function() {
                return [
                    'id' => $this->diet->id, 
                    'title' => $this->diet->title,
                    'calories' => $this->diet->calories,
                    // Add any other fields the app needs (e.g., protein, fat, carbs)
                ];
            }),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}