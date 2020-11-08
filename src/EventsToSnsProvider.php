<?php

namespace Ralbear\EventsToSns;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Ralbear\EventsToSns\Contracts\ShouldBeInSns;
use Ralbear\EventsToSns\Listeners\SendToSnsListener;

class EventsToSnsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AwsSnsService::class, function () {
            return new AwsSnsService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/events-to-sns.php' => config_path('events-to-sns.php'),
        ]);

        Event::listen(ShouldBeInSns::class, SendToSnsListener::class);
    }
}
