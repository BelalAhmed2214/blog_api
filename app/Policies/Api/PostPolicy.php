<?php

namespace App\Policies\Api;

use App\Models\User;
use App\Models\Role;

class PostPolicy
{
    public function view(User $user){
        return in_array($user->role_id,[Role::IS_ADMIN,Role::IS_EDITOR,Role::IS_USER]); 
    }
    public function modify(User $user){
        return in_array($user->role_id,[Role::IS_ADMIN,Role::IS_EDITOR]);
    }
    public function delete(User $user){
        return $user->role_id === Role::IS_ADMIN;
    }
     
}
