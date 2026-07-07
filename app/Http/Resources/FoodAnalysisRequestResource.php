<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodAnalysisRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'provider'          => $this->provider,
            'is_food'           => $this->is_food,
            'top_food_name'     => $this->top_food_name,
            'top_group'         => $this->top_group,
            'top_score'         => $this->top_score,
            'calories'          => $this->calories,
            'protein'           => $this->protein,
            'total_fat'         => $this->total_fat,
            'total_carbs'       => $this->total_carbs,
            'status'            => $this->status,
            'image'             => getSingleMedia($this, 'food_recognition_image', null),
            'response_json'     => $this->response_json,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
