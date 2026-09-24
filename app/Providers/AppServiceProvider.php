<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\AuditLog;

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
        Event::listen(Login::class, function ($event) {
            AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'login',
                'model_type' => null,
                'model_id' => null,
                'old_values' => null,
                'new_values' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                AuditLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'logout',
                    'model_type' => null,
                    'model_id' => null,
                    'old_values' => null,
                    'new_values' => null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        View::composer('*', function ($view) {
            $view->with('centerSettings', SettingController::getSettings());
        });
    }
}