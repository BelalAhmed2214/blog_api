<?php

namespace App\Http\Resources\Api;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->role_id == Role::IS_ADMIN){
            $role = "Admin";
        }
        else if($this->role_id== Role::IS_EDITOR){
            $role= "Editor";
        } 
        else{
            $role = "User";
        }
        return [
            "id"=>$this->id,
            "name"=>$this->name,
            "email"=>$this->email,
            'createdAt' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'role'=>$role
        ];
    }
}
