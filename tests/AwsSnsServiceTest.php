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
use Ralbear\EventsToSns\Model\Data;
use Ralbear\EventsToSns\Model\Env;
use Ralbear\EventsToSns\Model\Message;
use Ralbear\EventsToSns\Model\Metadata;
use Ralbear\EventsToSns\Model\Topic;
use Ralbear\EventsToSns\Model\Type;
use Ralbear\EventsToSns\Traits\SendToSns;

class AwsSnsServiceTest extends TestCase
{
    public function testValidEventInvokeSendMethodOnService()
    {
        Config::set('queue.connections.sqs-sns.valid_topics', ['topictest']);
        Config::set('queue.connections.sqs-sns.env_postfix', 'localtest');

        $serviceMock = $this->getMockBuilder(AwsSnsService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->enableArgumentCloning()
            ->getMock();

        $message = new Message(
            new Topic('topictest'),
            new Type('typetest'),
            new Env('localtest'),
            new Data(['user' => ['name' => 'pepito'], 'item' => ['foo' => 'bar']]),
            new Metadata(['foo' => 'bar'])
        );

        $serviceMock->expects($this->once())
            ->method('send')
            ->with(new Topic('topictest'), $message);

        $this->app->instance(AwsSnsService::class, $serviceMock);

        $event = new class implements ShouldBeInSns {
            use SendToSns;

            public $user = ['name' => 'pepito'];

            public $item = ['foo' => 'bar'];

            public function getTopic()
            {
                return 'topictest';
            }

            public function getType()
            {
                return 'typetest';
            }

            public function getMetadata()
            {
                return [
                    'foo' => 'bar'
                ];
            }
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

            public function getTopic(): string
            {
                return 'user';
            }
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

            public $item = ['foo' => 'bar'];

            public function getTopic(): string
            {
                return 'test:123';
            }
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

            public $item = ['foo' => 'bar'];

            public function getTopic(): string
            {
                return 'invoice';
            }
        };

        Event::dispatch($event);
    }
}
