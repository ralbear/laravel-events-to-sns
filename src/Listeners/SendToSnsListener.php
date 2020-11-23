<?php

declare(strict_types=1);

namespace Ralbear\EventsToSns\Listeners;

use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as Cache;
use Ralbear\EventsToSns\AwsSnsService;
use Ralbear\EventsToSns\Contracts\ShouldBeInSns;
use Ralbear\EventsToSns\Model\Data;
use Ralbear\EventsToSns\Model\Env;
use Ralbear\EventsToSns\Model\Message;
use Ralbear\EventsToSns\Model\Metadata;
use Ralbear\EventsToSns\Model\Topic;
use Ralbear\EventsToSns\Model\Type;

class SendToSnsListener
{
    protected $service;

    /**
     * @param AwsSnsService $service
     */
    public function __construct(AwsSnsService $service)
    {
        $this->service = $service;
    }

    /**
     * @param ShouldBeInSns $event
     * @return void
     * @throws \Ralbear\EventsToSns\Exceptions\MessageCantBeSendException
     */
    public function handle(ShouldBeInSns $event)
    {
        if ($this->shouldDispatch($event)) {

            $topic = new Topic($event->getTopic());

            $message = new Message(
                $topic,
                new Type($event->getType()),
                new Env($event->getEnv()),
                new Data($event->getData()),
                new Metadata($event->getMetadata())
            );

            $this->service->send($topic, $message);
        }
    }

    /**
     * @param ShouldBeInSns $event
     * @return bool
     */
    protected function shouldDispatch(ShouldBeInSns $event)
    {
        $lock = Container::getInstance()->make(Cache::class)->lock(
            sprintf('unique:%s%s', get_class($event), $event->uniqueId()),
            $event->validFor()
        );

        return (bool)$lock->get();
    }
}
