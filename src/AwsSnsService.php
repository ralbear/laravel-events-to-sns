<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns;


use Aws\Sns\Exception\SnsException;
use Aws\Sns\SnsClient;
use Ralbear\EventsToSns\Exceptions\MessageCantBeSendException;
use Ralbear\EventsToSns\Model\Message;
use Ralbear\EventsToSns\Model\Topic;

class AwsSnsService
{
    protected $client;

    public function __construct()
    {
        $this->connect();
    }

    protected function connect(): void
    {
        $connectionPayload = [
            'credentials' => [
                'key' => config('queue.connections.sqs-sns.key'),
                'secret' => config('queue.connections.sqs-sns.secret'),
            ],
            'region' => config('queue.connections.sqs-sns.region'),
            'version' => '2010-03-31'
        ];

        $this->client = new SnsClient($connectionPayload);
    }

    /**
     * @param Topic $topic
     * @param Message $message
     * @throws MessageCantBeSendException
     */
    public function send(Topic $topic, Message $message): void
    {
        try {
            $this->client->publish([
                'Message' => $message->getFormattedMessage(),
                'TopicArn' => $topic->getTopicArn(),
            ]);
        } catch (SnsException $e) {
            throw new MessageCantBeSendException($e);
        }
    }
}
