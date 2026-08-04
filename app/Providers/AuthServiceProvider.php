<?php

namespace App\Providers;

use App\Models\Jurnaling;
use App\Models\NeracaSaldo;
use App\Models\Otorisator;
use App\Models\Periode;
use App\Models\SaldoAwal;
use App\Models\User;
use App\Policies\JournalPolicy;
use App\Policies\LedgerPolicy;
use App\Policies\OtorisatorPolicy;
use App\Policies\PeriodePolicy;
use App\Policies\SaldoAwalPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Jurnaling::class => JournalPolicy::class,
        User::class => UserPolicy::class,
        NeracaSaldo::class => LedgerPolicy::class,
        Periode::class => PeriodePolicy::class,
        SaldoAwal::class => SaldoAwalPolicy::class,
        Otorisator::class => OtorisatorPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Feature-based gates
        Gate::define('export-journal', function (User $user) {
            return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator', 'bod']);
        });

        Gate::define('import-data', function (User $user) {
            return in_array($user->usertype, ['rootsuperuser', 'admin', 'operator']);
        });

        Gate::define('post-journal', function (User $user) {
            return in_array($user->usertype, ['rootsuperuser', 'admin']);
        });

        Gate::define('manage-users', function (User $user) {
            return in_array($user->usertype, ['rootsuperuser', 'admin']);
        });
    }
}
