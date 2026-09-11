<?php

namespace App\Providers;

use App\Services\PushNotificationService;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // PushNotificationService has no constructor dependencies; let the container auto-resolve it.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin / super_admin roles bypass every Gate check (Spatie integration)
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasRole') && $user->hasAnyRole(['admin', 'super_admin'])) {
                return true;
            }
        });

        Event::listen(NotificationFailed::class, function (NotificationFailed $event): void {
            if (! str_contains((string) $event->channel, 'Fcm')) {
                return;
            }
            $report = $event->data['report'] ?? null;
            $message = $report ? $report->error()?->getMessage() : 'unknown FCM failure';
            Log::warning('FCM notification failed', [
                'user_id' => $event->notifiable->getKey(),
                'notification' => $event->notification::class,
                'error' => $message,
            ]);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        TextInput::configureUsing(function (TextInput $component): void {
            if (app()->getLocale() !== 'ar') {
                return;
            }

            if (in_array($component->getType(), ['email', 'tel', 'url', 'number', 'password'], true)) {
                return;
            }

            $component->extraInputAttributes(['dir' => 'rtl'], merge: true);
        });

        Textarea::configureUsing(function (Textarea $component): void {
            if (app()->getLocale() !== 'ar') {
                return;
            }

            $component->extraInputAttributes(['dir' => 'rtl'], merge: true);
        });
    }
}
