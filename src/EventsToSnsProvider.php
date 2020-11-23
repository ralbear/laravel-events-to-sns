<?php

namespace Ralbear\EventsToSns;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Ralbear\EventsToSns\Connectors\SqsSnsConnector;
use Ralbear\EventsToSns\Contracts\ShouldBeInSns;
use Ralbear\EventsToSns\Listeners\SendToSnsListener;

class EventsToSnsProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $jobs;

    /**
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * @return array
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->app['queue']->extend('sqs-sns', function () {
            return new SqsSnsConnector($this->getJobs());
        });

        Event::listen(ShouldBeInSns::class, SendToSnsListener::class);
    }
}
