<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Model;


use Illuminate\Support\Facades\Config;
use Ralbear\EventsToSns\Exceptions\InvalidTopicFormatException;
use Ralbear\EventsToSns\Exceptions\TopicNotAllowedException;

class Topic
{
    public const MAX_LENGTH = 256;

    public const VALIDATION_REGEX = '/[^A-Za-z0-9-_]/';

    protected string $topic;

    public function setTopic(string $topic): void
    {
        $this->validateTopic($topic);

        $this->topic = $topic;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getTopicArn(): string
    {
        return sprintf('%s:%s-%s', config('events-to-sns.aws.base_ARN'), $this->topic, config('events-to-sns.topic.env_postfix'));
    }

    protected function validateTopic(string $topic): void
    {
        if (self::MAX_LENGTH < strlen($topic) || preg_match(self::VALIDATION_REGEX, $topic)) {
            throw new InvalidTopicFormatException;
        }

        if (!in_array($topic, (array) Config::get('events-to-sns.topic.valid'), true)) {
            throw new TopicNotAllowedException;
        }
    }
}
