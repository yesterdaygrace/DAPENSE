<?php

namespace App\Policies;

use App\Models\Jurnaling;
use App\Models\User;

class JournalPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator', 'bod']);
    }

    public function view(User $user, Jurnaling $jurnaling): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator', 'bod']);
    }

    public function create(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator']);
    }

    public function update(User $user, Jurnaling $jurnaling): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator']);
    }

    public function delete(User $user, Jurnaling $jurnaling): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator']);
    }

    public function export(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator', 'bod']);
    }

    public function import(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator']);
    }

    public function post(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin']);
    }

    public function rekap(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin']);
    }
}
