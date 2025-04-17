<?php

namespace App\Providers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // Gate for admin users
        Gate::define('access-admin-dashboard', function ($user) {
            return $user instanceof \App\Models\Admin;
        });

        // Gate for member users
        Gate::define('access-member-dashboard', function ($user) {
            return $user instanceof \App\Models\Staff;
        });
    }
}
