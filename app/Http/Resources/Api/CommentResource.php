<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'comment'=>$this->comment,
            'postId'=>$this->post_id,
            'userId'=>$this->user_id,
            'createdAt'=>Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updatedAt'=>Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'post'=>new PostResource($this->whenLoaded('post')),
            'user'=>new UserResource($this->whenLoaded('user')),
            
        ];
    }
}
