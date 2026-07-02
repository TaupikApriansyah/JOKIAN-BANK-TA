<?php

namespace App\Providers;

use App\Models\SlaNotification;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Services are auto-resolved by Laravel's container.
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request): Limit {
            $login = Str::lower((string) $request->input('login'));

            return Limit::perMinute(5)->by($login.'|'.$request->ip());
        });

        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();
            $unreadNotificationCount = $user
                ? SlaNotification::query()->where('recipient_id', $user->id)->whereNull('read_at')->count()
                : 0;

            $view->with('unreadNotificationCount', $unreadNotificationCount);
        });
    }
}
