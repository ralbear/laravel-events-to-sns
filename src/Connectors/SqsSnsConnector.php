<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Connectors;


use Aws\Sqs\SqsClient;
use Illuminate\Queue\Connectors\SqsConnector;
use Illuminate\Support\Arr;
use Ralbear\EventsToSns\Queues\SqsSnsQueue;

class SqsSnsConnector extends SqsConnector
{
    protected $jobs;

    /**
     * @param array $jobs
     */
    public function __construct(array $jobs)
    {
        $this->jobs = $jobs;
    }

    /**
     * @param array $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);

        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret']);
        }

        return new SqsSnsQueue(
            $this->jobs,
            new SqsClient($config),
            sprintf('%s/%s', $config['prefix'], $config['queue']),
            Arr::get($config, 'prefix', '')
        );
    }
}
