<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Contracts;


interface ShouldBeInSns
{
    /**
     * @return string
     */
    public function uniqueId();

    /**
     * @return int
     */
    public function validFor();

    /**
     * @return string
     */
    public function getTopic();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getEnv();

    /**
     * @return array
     */
    public function getData();

    /**
     * @return array
     */
    public function getMetadata();
}
