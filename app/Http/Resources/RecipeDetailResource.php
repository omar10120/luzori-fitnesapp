<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecipeDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $user_id = auth('sanctum')->id() ?? null;
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'type'             => $this->type,
            'meal_type'        => $this->meal_type,
            'description'      => $this->description,
            'preparation_time' => $this->preparation_time,
            'calories'         => round($this->calories),
            'protein'          => round($this->protein),
            'fats'             => round($this->fats),
            'carbs'            => round($this->carbs),
            'is_favourite'     => $this->userFavouriteRecipe->where('user_id',$user_id)->first() ? 1 : 0,
            'recipe_categories' => $this->categories->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->title
            ]),

            'recipe_tags' => $this->tags->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->title
            ]),           
        ];
    }
}