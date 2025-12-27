<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Allow admin users to bypass all authorization checks (including Policies)
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        $this->registerGates();
    }

    /**
     * Register all permission gates dynamically.
     */
    protected function registerGates(): void
    {
        try {
            $permissions = Permission::all();

            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    if ($user->isAdmin()) {
                        return true;
                    }
                    return $user->hasPermission($permission->slug);
                });
            }
        } catch (\Exception $e) {
            // Database might not be ready yet (during migrations)
        }
    }
}
