<?php

namespace App\Policies\Api;

use App\Models\Role;
use App\Models\User;

class CommentPolicy
{
    public function view_any(User $user)
    {
        return in_array($user->role_id, [Role::IS_ADMIN, Role::IS_EDITOR, Role::IS_USER]);
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
