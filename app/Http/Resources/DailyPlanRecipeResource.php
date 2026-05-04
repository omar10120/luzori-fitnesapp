<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyPlanRecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'daily_plan_id' => $this->daily_plan_id,
            'recipe_id'     => $this->recipe_id,
            'calories'      => round($this->calories),
            'protein'       => round($this->protein),
            'fats'          => round($this->fats),
            'carbs'         => round($this->carbs),
            'meal_type'     => $this->meal_type,
            'recipe'        => isset($this->recipe) ? new RecipePlanResource($this->recipe) : null,
            'is_complete'   => $this->is_complete,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
