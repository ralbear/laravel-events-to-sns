<?php

declare(strict_types=1);

namespace Ralbear\EventsToSns\Traits;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;

trait SendToSns
{
    /**
     * @return string
     */
    public function uniqueId()
    {
        return Str::uuid()->toString();
    }

    /**
     * @return int
     */
    public function validFor()
    {
        return 5;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $path = explode('\\', __CLASS__);
        $className = array_pop($path);

        if ($eventPostfix = config('queue.connections.sqs-sns.event_class_postfix')) {
            $className = preg_replace(
                '/' . preg_quote($eventPostfix, '/') . '$/',
                '',
                $className
            );
        }

        return Str::snake($className);
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return config('queue.connections.sqs-sns.env_postfix');
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getData()
    {
        $payload = [];

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $payload[$property->getName()] = $this->formatProperty($property->getValue($this));
        }

        return $payload;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return [];
    }

    /**
     * @param $value
     * @return array
     */
    protected function formatProperty($value)
    {
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        return $value;
    }
}
