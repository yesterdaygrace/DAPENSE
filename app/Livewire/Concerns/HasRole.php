<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasRole
{
    public function role(): string
    {
        return Auth::user()->usertype;
    }

    public function routePrefix(): string
    {
        return match ($this->role()) {
            'rootsuperuser' => 'rootsuperuser',
            'bod' => 'bod',
            'operator' => 'operator',
            default => 'admin',
        };
    }

    public function roleLabel(): string
    {
        return match ($this->role()) {
            'rootsuperuser' => 'Root Superuser',
            'bod' => 'BOD',
            'operator' => 'Operator',
            default => 'Admin',
        };
    }

    public function isRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array($this->role(), $roles);
    }

    public function canAccess(string $feature): bool
    {
        $permissions = [
            'dashboard' => ['rootsuperuser', 'admin', 'operator', 'bod'],
            'master-data' => ['rootsuperuser', 'admin', 'operator'],
            'transactions' => ['rootsuperuser', 'admin', 'operator'],
            'reports' => ['rootsuperuser', 'admin', 'operator', 'bod'],
            'finance' => ['rootsuperuser', 'admin', 'operator', 'bod'],
            'administration' => ['rootsuperuser', 'admin'],
            'settings' => ['rootsuperuser', 'admin', 'operator'],
            'jurnal-entry' => ['rootsuperuser', 'admin', 'operator'],
            'jurnaling' => ['rootsuperuser', 'admin', 'operator'],
            'bukubesar' => ['rootsuperuser', 'admin', 'operator', 'bod'],
            'neracasaldo' => ['rootsuperuser', 'admin', 'operator', 'bod'],
            'posting' => ['rootsuperuser', 'admin'],
            'otorisator' => ['rootsuperuser', 'admin', 'operator'],
            'users' => ['rootsuperuser', 'admin'],
            'saldoawal' => ['rootsuperuser', 'admin', 'operator'],
        ];

        return in_array($this->role(), $permissions[$feature] ?? []);
    }
}
