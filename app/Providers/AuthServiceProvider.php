<?php

namespace App\Providers;

use App\Models\Jurnaling;
use App\Models\User;
use App\Policies\JournalPolicy;
use App\Policies\LedgerPolicy;
use App\Policies\OtorisatorPolicy;
use App\Policies\PeriodePolicy;
use App\Policies\ReportPolicy;
use App\Policies\SaldoAwalPolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Jurnaling::class => JournalPolicy::class,
        User::class => UserPolicy::class,
        'App\Models\NeracaSaldo' => LedgerPolicy::class,
        'App\Models\Periode' => PeriodePolicy::class,
        'App\Models\SaldoAwal' => SaldoAwalPolicy::class,
        'App\Models\Otorisator' => OtorisatorPolicy::class,
        'App\Models\Setting' => SettingPolicy::class,
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
