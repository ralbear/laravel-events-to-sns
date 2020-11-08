<?php

declare(strict_types=1);

namespace Ralbear\EventsToSns\Listeners;

use Ralbear\EventsToSns\AwsSnsService;
use Ralbear\EventsToSns\Contracts\ShouldBeInSns;
use Ralbear\EventsToSns\Model\Message;
use Ralbear\EventsToSns\Model\Topic;

class SendToSnsListener
{
    protected AwsSnsService $service;

    public function __construct(AwsSnsService $service)
    {
        $this->service = $service;
    }

    public function handle(ShouldBeInSns $event)
    {
        $topic = new Topic();
        $topic->setTopic($event->getTopic());

        $message = new Message();
        $message->setData($event->getData());

        $this->service->send($topic, $message);
    }
}
