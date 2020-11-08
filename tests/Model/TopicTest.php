<?php

declare(strict_types=1);

namespace Ralbear\EventsToSns\Tests\Model;

use Ralbear\EventsToSns\Exceptions\InvalidTopicFormatException;
use Ralbear\EventsToSns\Model\Topic;
use Ralbear\EventsToSns\Tests\TestCase;

class TopicTest extends TestCase
{
    public function testSetTopicWithLongStringThrowExpectedException()
    {
        $this->expectException(InvalidTopicFormatException::class);

        $string257Characters = 'AHHccUGmZ7nfcibuz4pqu8Nma2Ah40gqgrj08QDX7xmAArD4t3UoTkUrIiHYtdMhsWjBwrZw55n29t77WHv6YIfWf8bYtqDFULgebFsrZnKhpyH3x9fYM2hOn5hBisNURJ43kdjEWq2EVTDFXuGGOV10x4TEiLPRt3047AhN7lKv9QMAqLq7z2nDkHE7J5w9xXBmOSRL4Ayon80BL0B10QQ9IqpN6B4GKCp8qowjSqeLJoNnHh00EufUz4eUFiLGL';

        $topic = new Topic();
        $topic->setTopic($string257Characters);
    }

    public function testSetTopicWithInvalidCharactersThrowExpectedException()
    {
        $this->expectException(InvalidTopicFormatException::class);

        $stringWithInvalidCharacters = 'test:some!more+things';

        $topic = new Topic();
        $topic->setTopic($stringWithInvalidCharacters);
    }
}
