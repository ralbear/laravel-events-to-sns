<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Model;


use Ralbear\EventsToSns\Exceptions\EmptyEventPayloadException;

class Message
{
    protected array $data;

    public function setData(array $data): void
    {
        if (empty($data)) {
            throw new EmptyEventPayloadException;
        }

        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getMessage(): array
    {
        return [
            'data' => $this->getData()
        ];
    }

    public function getFormattedMessage(): string
    {
        return json_encode($this->getMessage());
    }
}
