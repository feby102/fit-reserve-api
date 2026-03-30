<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

class AuthServiceProvider extends ServiceProvider
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
        
    ResetPassword::createUrlUsing(function ($user, string $token) {
        return "http://127.0.0.1:8000/reset-password/$token?email={$user->email}";
    });
    }
}
