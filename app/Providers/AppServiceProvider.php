<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share([
            'phoneNumber' => config('app.phone_number'),
            'telegramLink' => config('app.telegram_link'),
            'instagramLink' => config('app.instagram_link'),
            'vkLink' => config('app.vk_link'),
        ]);
    }
}
