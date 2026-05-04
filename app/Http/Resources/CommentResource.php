<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    
    public function toArray($request)
    {
        $comment_reply = $this->commentReply()->take(10)->get();
        return [
            'id'                => $this->id,
            'comment'           => $this->comment,
            'posting_id'        => $this->posting_id,
            'user_id'           => $this->user_id,
            'users'             => new PostingUserResource($this->user),
            'can_edit'          => $this->can_edit,
            'created_at'        => timeAgoFormate($this->created_at),
            'comment_reply_count'=> $this->comment_reply_count ?? $this->commentReply()->count(),
            'comment_reply'     => CommentReplyResource::collection($comment_reply),
        ];
    }
}
