<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Model;


class Env
{
    protected $env;

    /**
     * @param $env
     */
    public function __construct($env)
    {
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param string $env
     * @return void
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }


}
