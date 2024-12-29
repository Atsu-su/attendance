<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function register()
    {
        $this->app->when([AuthenticatedSessionController::class, AttemptToAuthenticate::class])
            ->needs(StatefulGuard::class)
            ->give(function () {
                if (request()->is('admin/*')) {
                    return Auth::guard('admin');
                }
                return Auth::guard('web');
            });
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
