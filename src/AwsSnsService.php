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
    protected SnsClient $client;

    public function __construct()
    {
        $this->connect();
    }

    protected function connect(): void
    {
        $connectionPayload = [
            'credentials' => [
                'key'    => config('events-to-sns.aws.key'),
                'secret' => config('events-to-sns.aws.secret'),
            ],
            'region' => config('events-to-sns.aws.region'),
            'version' => '2010-03-31'
        ];

        $this->client = new SnsClient($connectionPayload);
    }

    public function send(Topic $topic, Message $message): void
    {
        try {
            $this->client->publish([
                'Message' => $message->getFormattedMessage(),
                'TopicArn' => $topic->getTopicArn(),
            ]);
        } catch (SnsException $e) {
            dd($e->getMessage());
            throw new MessageCantBeSendException($e);
        }
    }
}
