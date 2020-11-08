<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Contracts;


interface ShouldBeInSns
{
    public function getTopic(): string;

    public function getData(): array;
}
