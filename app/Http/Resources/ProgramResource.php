<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'objective' => $this->objective,
            'duration_weeks' => $this->duration_weeks,
            'is_recurring' => (bool) $this->is_recurring,
            'frequency' => $this->frequency,
            'start_date' => $this->start_date,
            'status' => $this->status,
            
            // This is the only 'days' key you need
            'days' => ProgramDayResource::collection($this->whenLoaded('days')),

            // ADDED: Summary counts for the UI (Screenshot 8)
            'summary' => $this->whenLoaded('days', function() {
                return [
                    'total_days' => $this->days->count(),
                    'total_workouts' => $this->days->whereIn('type', ['workout', 'cardio'])->count(),
                    'total_diets' => $this->days->whereNotNull('diet_id')->count(),
                    'total_rest_days' => $this->days->where('type', 'rest')->count(),
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}