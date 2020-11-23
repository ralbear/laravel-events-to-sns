<?php

declare(strict_types=1);


namespace Ralbear\EventsToSns\Queues;


use Aws\Sqs\SqsClient;
use Illuminate\Queue\Jobs\SqsJob;
use Illuminate\Queue\SqsQueue;
use Ralbear\EventsToSns\Jobs\SqsSnsJob;

class SqsSnsQueue extends SqsQueue
{
    protected $jobs;

    /**
     * @param array $jobs
     * @param SqsClient $sqs
     * @param $default
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct(array $jobs, SqsClient $sqs, $default, $prefix = '', $suffix = '')
    {
        parent::__construct($sqs, $default, $prefix, $suffix);

        $this->jobs = $jobs;
    }

    /**
     * @param string $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queueName = null)
    {
        $queue = $this->getQueue($queueName);

        $response = $this->sqs->receiveMessage([
            'QueueUrl' => $queue,
            'AttributeNames' => ['ApproximateReceiveCount'],
        ]);

        if (isset($response['Messages']) && count($response['Messages']) > 0) {

            $message = $response['Messages'][0];
            $messageBody = json_decode($message['Body'], true);
            $bodyMessage = json_decode($messageBody['Message'], true);

            $type = $this->getType($bodyMessage);

            if (array_key_exists($type, $this->jobs)) {
                $jobClass = $this->jobs[$type];

                $response = $this->modifyPayload($message, $jobClass);

                return new SqsJob(
                    $this->container,
                    $this->sqs,
                    $response,
                    $this->connectionName,
                    $queue
                );
            }
        }
        return null;
    }

    /**
     * @param array $message
     * @return string
     */
    protected function getTopic($message)
    {
        return $message['topic'];
    }

    /**
     * @param array $message
     * @return string
     */
    protected function getType($message)
    {
        return $message['type'];
    }

    /**
     * @param $message
     * @return mixed
     */
    protected function getEnv($message)
    {
        return $message['env'];
    }

    /**
     * @param array $message
     * @return array
     */
    protected function getData($message)
    {
        return $message['data'];
    }

    /**
     * @param $message
     * @return array
     */
    protected function getMetadata($message)
    {
        return $message['metadata'] ?? [];
    }

    /**
     * @param array $message
     * @return array
     */
    protected function getJobDataPayload($message)
    {
        $result = json_decode($message['Message'], true);

        return [
            'topic' => $this->getTopic($result),
            'type' => $this->getType($result),
            'env' => $this->getEnv($result),
            'data' => $this->getData($result),
            'metadata' => $this->getMetadata($result),
        ];
    }

    /**
     * @param array $message
     * @return array
     */
    protected function getJobPayload($message)
    {
        return [
            'job' => $this->getClassHandler($message),
            'data' => $this->getJobDataPayload($message)
        ];
    }

    /**
     * @param array $payload
     * @param string $class
     * @return array
     */
    private function modifyPayload($payload, $class)
    {
        $body = [
            'uuid' => $payload['MessageId'],
            'displayName' => $class,
            'job' => sprintf('%s@handle', $class),
            'data' => $this->getJobDataPayload(json_decode($payload['Body'], true))
        ];

        $payload['Body'] = json_encode($body);

        return $payload;
    }
}
