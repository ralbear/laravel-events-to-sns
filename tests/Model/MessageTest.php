<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Tests\Model;


use Illuminate\Support\Facades\Config;
use Ralbear\EventsToSns\Exceptions\EmptyEventPayloadException;
use Ralbear\EventsToSns\Exceptions\TopicNotAllowedException;
use Ralbear\EventsToSns\Model\Data;
use Ralbear\EventsToSns\Model\Env;
use Ralbear\EventsToSns\Model\Message;
use Ralbear\EventsToSns\Model\Metadata;
use Ralbear\EventsToSns\Model\Topic;
use Ralbear\EventsToSns\Model\Type;
use Ralbear\EventsToSns\Tests\TestCase;

class MessageTest extends TestCase
{
    public function testNotValidTopicThrowExpectedException()
    {
        $this->expectException(TopicNotAllowedException::class);

        Config::set('queue.connections.sqs-sns.valid_topics', ['topictest']);

        $topic = new Topic('wrongtopictest');
        $type = new Type('typetest');
        $env = new Env('envtest');
        $data = new Data(['foo' => 'bar']);
        $metadata = new Metadata(['foo' => 'bar']);

        new Message(
            $topic,
            $type,
            $env,
            $data,
            $metadata
        );
    }

    public function testEmptyDataThrowExpectedException()
    {
        $this->expectException(EmptyEventPayloadException::class);

        Config::set('queue.connections.sqs-sns.valid_topics', ['topictest']);

        $topic = new Topic('topictest');
        $type = new Type('typetest');
        $env = new Env('envtest');
        $data = new Data([]);
        $metadata = new Metadata(['foo' => 'bar']);

        new Message(
            $topic,
            $type,
            $env,
            $data,
            $metadata
        );
    }

    public function testCorrectDataReturnsExpectedFormattedMessage()
    {
        Config::set('queue.connections.sqs-sns.valid_topics', ['topictest']);

        $topic = new Topic('topictest');
        $type = new Type('typetest');
        $env = new Env('envtest');
        $data = new Data(['foo' => 'bar']);
        $metadata = new Metadata(['foo' => 'bar']);

        $message = new Message(
            $topic,
            $type,
            $env,
            $data,
            $metadata
        );

        $expectedMessage = '{
            "topic": "topictest",
            "type": "typetest",
            "env": "envtest",
            "data": {
                "foo": "bar"
            },
            "metadata": {
                "foo":"bar"
            }
        }';

        $this->assertJsonStringEqualsJsonString($expectedMessage, $message->getFormattedMessage());
    }
}
