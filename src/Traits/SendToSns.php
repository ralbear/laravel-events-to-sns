<?php

declare(strict_types=1);

namespace Ralbear\EventsToSns\Traits;

use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;

trait SendToSns
{
    public function getData(): array {
        $payload = [];

        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $payload[$property->getName()] = $this->formatProperty($property->getValue($this));
        }

        return $payload;
    }

    protected function formatProperty($value)
    {
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        return $value;
    }
}
