<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageExerciseResource extends JsonResource
{
    public function toArray($request)
    {
        $original = $this->relationLoaded('originalExercise')
            ? $this->originalExercise
            : $this->originalExercise()->first();

        return [
            'id'              => $this->id,
            'exercise_id'     => $this->exercise_id,
            'title'           => $this->title,
            'status'          => $this->status,
            'is_premium'      => $this->is_premium,
            'exercise_image'  => $original ? getSingleMedia($original, 'exercise_image', null) : null,
            'instruction'     => $this->instruction,
            'tips'            => $this->tips,
            'video_type'      => $this->video_type,
            'video_url'       => $this->video_url,
            'duration'        => $this->duration,
            'sets'            => $this->sets,
            'based'           => $this->based,
            'type'            => $this->type,
            'seconds_per_rep' => $this->seconds_per_rep ?? 4,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
