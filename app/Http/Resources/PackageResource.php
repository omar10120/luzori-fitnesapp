<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'id'                     => $this->id,
            'name'                   => $this->name,
            'duration'               => $this->duration,
            'duration_unit'          => $this->duration_unit,
            'price'                  => $this->price,
            'description'            => $this->description,
            'status'                 => $this->status,
            'diet_id'                => $this->diet_id,
            'advice_id'              => $this->advice_id,
            'exercise_id'            => $this->exercise_id,
            'food_recognition_limit' => $this->food_recognition_limit,
            'follow_up_price'        => $this->follow_up_price,
            'diet'                   => $this->when(
                $this->relationLoaded('diet'),
                fn () => $this->diet ? new DietResource($this->diet) : null
            ),
            'advice'                 => $this->when(
                $this->relationLoaded('advice'),
                fn () => $this->advice ? new AdviceResource($this->advice) : null
            ),
            'exercise'               => $this->when(
                $this->relationLoaded('packageExercise') || $this->relationLoaded('exercise'),
                function () {
                    if ($this->packageExercise) {
                        return new PackageExerciseResource($this->packageExercise);
                    }

                    return $this->exercise ? new ExerciseResource($this->exercise) : null;
                }
            ),
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
