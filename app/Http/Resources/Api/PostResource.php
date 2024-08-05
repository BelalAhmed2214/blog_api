<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->is_active == 1){
            $is_active = true;
        }
        else{
            $is_active = false;
        
        }

        return [
            "id"=>$this->id,
            "title"=>$this->title,
            "description"=>$this->description,
            "is_active"=>$is_active,
            "user_id"=>$this->user->id,
            'createdAt' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'user'=>new UserResource($this->whenLoaded('user')) 
            
        ];
    }
}
