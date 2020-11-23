<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Model;


class Metadata
{
    protected $metadata;

    /**
     * @param array $metadata
     */
    public function __construct($metadata = [])
    {
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     * @return void
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }


}
