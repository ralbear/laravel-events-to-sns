<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Tests;


use Ralbear\EventsToSns\Connectors\SqsSnsConnector;
use Ralbear\EventsToSns\Queues\SqsSnsQueue;

class SqsSnsConnectorTest extends TestCase
{
    public function testCanConnectToQueue()
    {
        $queue = (new SqsSnsConnector([]))->connect([
            'key' => 'test_key',
            'secret' => 'test_secret',
            'region' => 'eu-west-1',
            'queue' => '',
            'prefix' => ''
        ]);

        $this->assertInstanceOf(SqsSnsQueue::class, $queue);
    }
}
