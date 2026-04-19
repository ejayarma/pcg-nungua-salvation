<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Model::unguard();

        Gate::define('restore-audit', function (User $user, mixed $auditable): bool {
            Log::info('Checking Restore Audits permission for user: ' . $user->email);
            $allowedEmails = array_filter(array_map('trim', config('audit.restore.allowed_emails', [])));

            return $user->is_admin && in_array($user->email, $allowedEmails, true);
        });
    }
}
