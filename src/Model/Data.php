<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Model;


use Ralbear\EventsToSns\Exceptions\EmptyEventPayloadException;

class Data
{
    protected $data;

    /**
     * @param $data
     * @throws EmptyEventPayloadException
     */
    public function __construct($data)
    {
        $this->setData($data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return void
     * @throws EmptyEventPayloadException
     */
    public function setData($data)
    {
        $this->validateData($data);

        $this->data = $data;
    }

    /**
     * @param $data
     * @throws EmptyEventPayloadException
     */
    protected function validateData($data)
    {
        if (empty($data)) {
            throw new EmptyEventPayloadException();
        }
    }
}
