<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Tests;


use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;
use Illuminate\Queue\Jobs\SqsJob;
use Illuminate\Support\Str;
use Ralbear\EventsToSns\Queues\SqsSnsQueue;

class SqsSnsQueueTest extends TestCase
{
    public function testWillSetRoutes()
    {
        $jobs = [
            'test_event' => 'TestEventJob'
        ];

        $sqsClient = $this->getMockBuilder(SqsClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['receiveMessage'])
            ->getMock();

        $queue = new SqsSnsQueue($jobs, $sqsClient, 'test_queue', '', '');

        $queueReflection = new \ReflectionClass($queue);
        $routeReflectionProperty = $queueReflection->getProperty('jobs');
        $routeReflectionProperty->setAccessible(true);

        $this->assertEquals($jobs, $routeReflectionProperty->getValue($queue));
    }

    public function testWillCallReceiveMessage()
    {
        $sqsClient = $this->getMockBuilder(SqsClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['receiveMessage'])
            ->getMock();

        $sqsClient->expects($this->once())
            ->method('receiveMessage')
            ->willReturn([
                'Messages' => [],
            ]);

        $queue = new SqsSnsQueue([], $sqsClient, 'test_queue');
        $queue->setContainer($this->createMock(Container::class));

        $queue->pop();
    }

    public function testWillPopMessageOffQueue()
    {
        $sqsClient = $this->getMockBuilder(SqsClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['receiveMessage'])
            ->getMock();

        $jobs = [
            'test_type' => 'TestJob'
        ];

        $data = [
            'topic' => 'test_topic',
            'type' => 'test_type',
            'env' => 'test_env',
            'data' => [
                'foo' => 'bar'
            ],
            'metadata' => [],
        ];

        $message = [
            'MessageId' => Str::uuid()->toString(),
            'Body' => json_encode([
                'Message' => json_encode($data)
            ])
        ];

        $expectedRawBody = json_encode([
            'uuid' => $message['MessageId'],
            'displayName' => $jobs[$data['type']],
            'job' => 'TestJob@handle',
            'data' => $data
        ]);

        $sqsClient->method('receiveMessage')->willReturn([
            'Messages' => [
                $message,
            ],
        ]);

        $queue = new SqsSnsQueue($jobs, $sqsClient, 'test_queue');
        $queue->setContainer($this->createMock(Container::class));

        $job = $queue->pop();

        $this->assertInstanceOf(SqsJob::class, $job);
        $this->assertEquals($expectedRawBody, $job->getRawBody());
    }
}
