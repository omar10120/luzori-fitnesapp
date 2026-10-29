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
            'is_recurring' => $this->is_recurring,
            'frequency' => $this->frequency,
            'status' => $this->status,
            'days' => ProgramDayResource::collection($this->whenLoaded('days')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
