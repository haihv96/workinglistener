<?php

namespace App\Providers;

use App\Services\ThirdParty\Azure\BotAuthService;
use App\Services\ThirdParty\Azure\ChatbotService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('azure_bot_auth', function ($app) {
            return new BotAuthService(
                'https://login.microsoftonline.com',
                env('AZURE_CLIENT_ID'),
                env('AZURE_CLIENT_SECRET')
            );
        });

        $this->app->bind('azure_bot_service', function ($app) {
            return new ChatbotService();
        });
    }
}
