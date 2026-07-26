<?php

namespace App\Policies;

use App\Models\User;

class OtorisatorPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator']);
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator']);
    }

    public function update(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator']);
    }

    public function delete(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin']);
    }
}
