<?php

namespace App\Policies;

use App\Models\User;

class LedgerPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator', 'bod']);
    }

    public function export(User $user): bool
    {
        return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator', 'bod']);
    }
}
