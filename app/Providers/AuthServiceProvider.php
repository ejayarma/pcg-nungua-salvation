<?php

namespace App\Providers;

use App\Models\GenerationalGroup;
use App\Models\Member;
use App\Models\MessageBroadcast;
use App\Models\User;
use App\Policies\GenerationalGroupPolicy;
use App\Policies\MemberPolicy;
use App\Policies\MessageBroadcastPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        MessageBroadcast::class => MessageBroadcastPolicy::class,
        Member::class => MemberPolicy::class,
        GenerationalGroup::class => GenerationalGroupPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
