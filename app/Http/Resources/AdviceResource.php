<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdviceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'seed_text'  => $this->seed_text,
            'status'     => $this->status,
            'options'    => $this->whenLoaded('options', function () {
                return $this->options->map(function ($option) {
                    return [
                        'id'          => $option->id,
                        'key'         => $option->key,
                        'label'       => $option->label,
                        'description' => $option->description,
                        'order'       => $option->order,
                        'is_active'   => $option->is_active,
                        'is_required' => (bool) optional($option->pivot)->is_required,
                    ];
                })->values();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
