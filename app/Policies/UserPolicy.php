<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin']);
    }

    public function update(User $user, User $target): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin']);
    }

    public function delete(User $user, User $target): bool
    {
        return $user->usertype === 'rootsuperuser' && $user->id !== $target->id;
    }
}
