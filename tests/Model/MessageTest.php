<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Tests\Model;


use Ralbear\EventsToSns\Exceptions\EmptyEventPayloadException;
use Ralbear\EventsToSns\Exceptions\EventsToSnsException;
use Ralbear\EventsToSns\Model\Message;
use Ralbear\EventsToSns\Tests\TestCase;

class MessageTest extends TestCase
{
    public function testEmptyPayloadThrowExpectedException()
    {
        $this->expectException(EmptyEventPayloadException::class);
        $this->expectException(EventsToSnsException::class);

        $emptyPayload = [];

        $message = new Message();
        $message->setData($emptyPayload);
    }
}
