<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\BodyPart;

class ExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /*
        $selected_bodypart = null;
        if(isset($this->bodypart_ids)) {
            $selected_bodypart = BodyPart::whereIn('id', $this->bodypart_ids)->get()->map(function ($item) {
                // return $item;
                return [
                    'id'    => $item->id,
                    'title' => $item->title,
                    'bodypart_image'   => getSingleMedia($item, 'bodypart_image',null),
                ];
            });
        }
        */
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'status'        => $this->status,
            'is_premium'    => $this->is_premium,
            'exercise_image'=> getSingleMedia($this, 'exercise_image', null),
            /*
            'instruction'   => $this->instruction,
            'bodypart_name' => $selected_bodypart,
            'level_id'      => $this->level_id,
            'level_title'   => optional($this->level)->title,
            */
            'duration'      => $this->duration,
            'sets'          => $this->sets,
            'based'         => $this->based,
            'type'          => $this->type,
            'seconds_per_rep' => $this->seconds_per_rep ?? 4,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}