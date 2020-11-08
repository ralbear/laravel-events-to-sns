<?php

declare(strict_types=1);

namespace Ralbear\EventsToSns\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Ralbear\EventsToSns\AwsSnsService;
use Ralbear\EventsToSns\Contracts\ShouldBeInSns;
use Ralbear\EventsToSns\Exceptions\EmptyEventPayloadException;
use Ralbear\EventsToSns\Exceptions\EventsToSnsException;
use Ralbear\EventsToSns\Exceptions\InvalidTopicFormatException;
use Ralbear\EventsToSns\Exceptions\TopicNotAllowedException;
use Ralbear\EventsToSns\Model\Message;
use Ralbear\EventsToSns\Model\Topic;
use Ralbear\EventsToSns\Traits\SendToSns;

class AwsSnsServiceTest extends TestCase
{
    public function testValidEventInvokeSendMethodOnService()
    {
        Config::set('events-to-sns', ['topic' => ['valid' => ['user', 'order']]]);

        $serviceMock = $this->getMockBuilder(AwsSnsService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->enableArgumentCloning()
            ->getMock();

        $topic = new Topic();
        $topic->setTopic('user');

        $message = new Message();
        $message->setData(['user' => ['name' => 'pepito'],'item' => ['foo' => 'bar']]);

        $serviceMock->expects($this->once())
            ->method('send')
            ->with($topic, $message);

        $this->app->instance(AwsSnsService::class, $serviceMock);

        $event = new class implements ShouldBeInSns {
            use SendToSns;

            public array $user  = ['name' => 'pepito'];

            public array $item  = ['foo' => 'bar'];

            public function getTopic(): string { return 'user'; }
        };

        Event::dispatch($event);
    }

    public function testEmptyEventThrowExpectedException()
    {
        $this->expectException(EmptyEventPayloadException::class);
        $this->expectException(EventsToSnsException::class);

        Config::set('events-to-sns', ['topic' => ['valid' => ['user', 'order']]]);

        $serviceMock = $this->getMockBuilder(AwsSnsService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->enableArgumentCloning()
            ->getMock();

        $this->app->instance(AwsSnsService::class, $serviceMock);

        $event = new class implements ShouldBeInSns {
            use SendToSns;

            public function getTopic(): string { return 'user'; }
        };

        Event::dispatch($event);
    }

    public function testInvalidTopicThrowExpectedException()
    {
        $this->expectException(InvalidTopicFormatException::class);
        $this->expectException(EventsToSnsException::class);

        Config::set('events-to-sns', ['topic' => ['valid' => ['user', 'order']]]);

        $serviceMock = $this->getMockBuilder(AwsSnsService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->enableArgumentCloning()
            ->getMock();

        $this->app->instance(AwsSnsService::class, $serviceMock);

        $event = new class implements ShouldBeInSns {
            use SendToSns;

            public array $item  = ['foo' => 'bar'];

            public function getTopic(): string { return 'test:123'; }
        };

        Event::dispatch($event);
    }

    public function testNotAllowedTopicThrowExpectedException()
    {
        $this->expectException(TopicNotAllowedException::class);
        $this->expectException(EventsToSnsException::class);

        Config::set('events-to-sns', ['topic' => ['valid' => ['user', 'order']]]);

        $serviceMock = $this->getMockBuilder(AwsSnsService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->enableArgumentCloning()
            ->getMock();

        $this->app->instance(AwsSnsService::class, $serviceMock);

        $event = new class implements ShouldBeInSns {
            use SendToSns;

            public array $item  = ['foo' => 'bar'];

            public function getTopic(): string { return 'invoice'; }
        };

        Event::dispatch($event);
    }
}
