<?php

namespace App\Policies\Api;

use App\Models\Role;
use App\Models\User;

class UserPolicy
{
    public function view(User $user)
    {
        return $user->role_id === Role::IS_ADMIN;
    }
    public function modify(User $user)
    {
        return in_array($user->role_id, [Role::IS_ADMIN, Role::IS_EDITOR]);
    }
    public function delete(User $user)
    {
        return $user->role_id === Role::IS_ADMIN;
    }

}
