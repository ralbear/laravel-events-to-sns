<?php

declare(strict_types=1);

namespace Ralbear\EventsToSns\Tests\Model;

use Illuminate\Support\Facades\Config;
use Ralbear\EventsToSns\Exceptions\InvalidTopicFormatException;
use Ralbear\EventsToSns\Exceptions\TopicNotAllowedException;
use Ralbear\EventsToSns\Model\Topic;
use Ralbear\EventsToSns\Tests\TestCase;

class TopicTest extends TestCase
{
    public function testSetTopicWithLongStringThrowExpectedException()
    {
        $this->expectException(InvalidTopicFormatException::class);

        $string257Characters = 'AHHccUGmZ7nfcibuz4pqu8Nma2Ah40gqgrj08QDX7xmAArD4t3UoTkUrIiHYtdMhsWjB\
            wrZw55n29t77WHv6YIfWf8bYtqDFULgebFsrZnKhpyH3x9fYM2hOn5hBisNURJ43kdjEWq2EVTDFXuGGOV10x4TEi\
            LPRt3047AhN7lKv9QMAqLq7z2nDkHE7J5w9xXBmOSRL4Ayon80BL0B10QQ9IqpN6B4GKCp8qowjSqeLJoNnHh00EufUz4eUFiLGL';

        new Topic($string257Characters);
    }

    public function testSetTopicWithInvalidCharactersThrowExpectedException()
    {
        $this->expectException(InvalidTopicFormatException::class);

        $stringWithInvalidCharacters = 'test:some!more+things';

        new Topic($stringWithInvalidCharacters);
    }

    public function testNonValidTopicThrowExpectedException()
    {
        $this->expectException(TopicNotAllowedException::class);

        Config::set('queue.connections.sqs-sns.valid_topics', ['topictest']);

        new Topic('wrongtopictest');
    }
}
