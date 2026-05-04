<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Tags;
use App\Models\Category;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $tags_name = null;
        if(isset($this->tags_id)) {
            $tags_name = Tags::whereIn('id', $this->tags_id)->get()->map(function ($item) {
                return [
                    'id'    => $item->id,
                    'title' => $item->title,
                ];
            });
        }
                
        $category_name = null;
        if(isset($this->category_ids)) {
            $category_name = Category::whereIn('id', $this->category_ids)->get()->map(function ($item) {
                return [
                    'id'    => $item->id,
                    'title' => $item->title,
                ];
            });
        }

        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'tags_name'         => $tags_name,
            'tags_id'           => $this->tags_id,
            'category_ids'      => $this->category_ids,
            'category_name'     => $category_name,
            'datetime'          => $this->datetime,
            'status'            => $this->status,
            'is_featured'       => $this->is_featured,
            'post_image'        => getSingleMedia($this, 'post_image',null),
            // 'description'       => $this->description,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
