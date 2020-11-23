<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Model;


class Message
{
    protected $topic;

    protected $type;

    protected $env;

    protected $data;

    protected $metadata;

    /**
     * @param Topic $topic
     * @param Type $type
     * @param Env $env
     * @param Data $data
     * @param Metadata $metadata
     */
    public function __construct(Topic $topic, Type $type, Env $env, Data $data, Metadata $metadata)
    {
        $this->topic = $topic;
        $this->type = $type;
        $this->env = $env;
        $this->data = $data;
        $this->metadata = $metadata;
    }

    /**
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param Topic $topic
     * @return void
     */
    public function setTopic(Topic $topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Type $type
     * @return void
     */
    public function setType(Type $type)
    {
        $this->type = $type;
    }

    /**
     * @return Env
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param Env $env
     * @return void
     */
    public function setEnv(Env $env)
    {
        $this->env = $env;
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param Metadata $metadata
     * @return void
     */
    public function setMetadata(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function getMessage()
    {
        return [
            'topic' => $this->topic->getTopic(),
            'type' => $this->type->getType(),
            'env' => $this->env->getEnv(),
            'data' => $this->data->getData(),
            'metadata' => $this->metadata->getMetadata(),
        ];
    }

    /**
     * @return string
     */
    public function getFormattedMessage()
    {
        return json_encode($this->getMessage());
    }
}
