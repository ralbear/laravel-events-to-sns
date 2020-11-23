<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Model;


use Ralbear\EventsToSns\Exceptions\InvalidTopicFormatException;
use Ralbear\EventsToSns\Exceptions\TopicNotAllowedException;

class Topic
{
    public const MAX_LENGTH = 256;

    public const VALIDATION_REGEX = '/[^A-Za-z0-9-_]/';

    protected $topic;

    /**
     * @param $topic
     * @throws InvalidTopicFormatException
     * @throws TopicNotAllowedException
     */
    public function __construct($topic)
    {
        $this->setTopic($topic);
    }

    /**
     * @param string $topic
     * @return void
     * @throws TopicNotAllowedException
     * @throws InvalidTopicFormatException
     */
    public function setTopic(string $topic)
    {
        $this->validateTopic($topic);

        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @return string
     */
    public function getTopicArn()
    {
        return sprintf(
            '%s:%s-%s',
            config('queue.connections.sqs-sns.base_ARN'),
            $this->topic,
            config('queue.connections.sqs-sns.env_postfix')
        );
    }

    /**
     * @param string $topic
     * @return void
     * @throws TopicNotAllowedException
     * @throws InvalidTopicFormatException
     */
    protected function validateTopic(string $topic)
    {
        if (self::MAX_LENGTH < strlen($topic) || preg_match(self::VALIDATION_REGEX, $topic)) {
            throw new InvalidTopicFormatException;
        }

        if (!in_array($topic, (array)config('queue.connections.sqs-sns.valid_topics'), true)) {
            throw new TopicNotAllowedException;
        }
    }
}
