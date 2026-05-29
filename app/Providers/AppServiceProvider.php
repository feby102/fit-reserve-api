<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\Vendor;
use Laravel\Sanctum\PersonalAccessToken;
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
    
    \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
        'stadium'   => \App\Models\Stadium::class,
        'academy'   => \App\Models\Academy::class,
        'coach'     => \App\Models\PrivateCoach::class,
        'gym'       => \App\Models\Gym::class,
        'challenge' => \App\Models\Challenge::class,
    ]);
    
    \Illuminate\Support\Facades\Broadcast::routes(['middleware' => ['auth:api']]);
    require base_path('routes/channels.php');

    Sanctum::authenticateAccessTokensUsing(function ($accessToken, $isValid) {
        if (! $isValid) return false;

        // طالما التوكن جاي من موديل فيندور، اقلب البروفايدر فوراً لـ vendors
        if ($accessToken->tokenable_type === Vendor::class) {
            config(['auth.guards.sanctum.provider' => 'vendors']);
        } else {
            config(['auth.guards.sanctum.provider' => 'users']);
        }

        return true;
    });
}}
