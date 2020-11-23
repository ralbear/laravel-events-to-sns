<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Jobs;


use Aws\Sqs\SqsClient;
use Illuminate\Queue\Jobs\SqsJob;
use Illuminate\Container\Container;
use Illuminate\Queue\CallQueuedHandler;

class SqsSnsJob extends SqsJob
{
    /**
     * @param \Illuminate\Container\Container $container
     * @param \Aws\Sqs\SqsClient $sqs
     * @param string $queue
     * @param array $job
     * @param string $connectionName
     * @param array $routes
     * @return void
     */
    public function __construct(
        Container $container,
        SqsClient $sqs,
        array $job,
        $connectionName,
        $queue,
        array $routes
    )
    {
        parent::__construct($container, $sqs, $job, $connectionName, $queue);

        $this->job = $this->resolveSnsSubscription($this->job, $routes);
    }

    /**
     * @param array $job
     * @param array $routes
     * @return array
     */
    protected function resolveSnsSubscription(array $job, array $routes)
    {
        $body = json_decode($job['Body'], true);

        $commandName = null;

        $possibleRouteParams = ['Subject', 'TopicArn'];

        foreach ($possibleRouteParams as $param) {
            if (isset($body[$param]) && array_key_exists($body[$param], $routes)) {
                // Find name of command in queue routes using the param field
                $commandName = $routes[$body[$param]];
                break;
            }
        }

        if ($commandName !== null) {

            $command = $this->makeCommand($commandName, $body);

            $job['Body'] = json_encode([
                'displayName' => $commandName,
                'job' => CallQueuedHandler::class . '@call',
                'data' => compact('commandName', 'command'),
            ]);
        }

        return $job;
    }

    /**
     * @param string $commandName
     * @param array $body
     * @return string
     */
    protected function makeCommand($commandName, $body)
    {
        $payload = json_decode($body['Message'], true);

        $data = [
            'subject' => (isset($body['Subject'])) ? $body['Subject'] : '',
            'payload' => $payload
        ];

        $instance = $this->container->make($commandName, $data);

        return serialize($instance);
    }

    /**
     * @return array
     */
    public function getSqsSnsJob()
    {
        return $this->job;
    }
}
