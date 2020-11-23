<p align="center">
<a href="https://packagist.org/packages/ralbear/laravel-events-to-sns"><img src="https://img.shields.io/packagist/dt/ralbear/laravel-events-to-sns" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/ralbear/laravel-events-to-sns"><img src="https://img.shields.io/packagist/v/ralbear/laravel-events-to-sns" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/ralbear/laravel-events-to-sns"><img src="https://img.shields.io/packagist/l/ralbear/laravel-events-to-sns" alt="License"></a>
</p>

## Laravel events to SNS

This library allow us to send Laravel events to an SNS topic, and receive them through a SQS queue.

## Install

```shell script
$ composer require ralbear/laravel-events-to-sns
```
## Configuration

First step is create this new connection configuration in `config/queue.php`

```
'connections' => [
    'sqs-sns' => [
        'driver' => 'sqs-sns',
        'key' => env('SQS_SNS_ACCESS_KEY_ID') ?? env('AWS_ACCESS_KEY_ID') ?? '',
        'secret' => env('SQS_SNS_SECRET_ACCESS_KEY') ?? env('AWS_SECRET_ACCESS_KEY') ?? '',
        'region' => env('SQS_SNS_DEFAULT_REGION') ?? env('AWS_DEFAULT_REGION') ?? '',
        'base_ARN' => env('SQS_SNS_BASE_ARN') ?? '',
        'valid_topics' => explode(',',env('SQS_SNS_VALID_TOPICS')) ?? [],
        'prefix' => env('SQS_SNS_PREFIX') ?? env('SQS_PREFIX') ?? '',
        'queue' => env('SQS_SNS_QUEUE') ?? env('SQS_QUEUE') ?? '',
        'env_postfix' => env('SQS_SNS_ENV') ?? env('APP_ENV') ?? '',
        'event_class_postfix' => 'Event'
    ],
]
```

### AWS credentials

If we use the same AWS account for SNS than for other AWS services on the application, we can use the default env keys for the credentials.

```
AWS_ACCESS_KEY_ID=<MAIN ACCESS KEY ID>
AWS_SECRET_ACCESS_KEY=<SECRECT ACCESS KEY>
AWS_DEFAULT_REGION=us-west-1
```
If we need specific credentials for SNS, use this env keys:

```
SQS_SNS_ACCESS_KEY_ID=<SNS ACCESS KEY ID>
SQS_SNS_SECRET_ACCESS_KEY=<SNS SECRET ACCESS KEY>
SQS_SNS_DEFAULT_REGION=eu-west-1
```

### Topics

The way this library is designed, define SNS topics based on three parts.

![ARN topic parts](https://raw.githubusercontent.com/ralbear/laravel-events-to-sns/docs/media/arn_topic_parts.png)
 
 - A: Use the env variable:
```
SQS_SNS_BASE_ARN=arn:aws:sns:eu-west-1:123456789
```
 - B: Defined in your event:
 ```PHP
public function getTopic()
{
    return 'service-a-topic';
}
```
The event level topics we use, should be defined as a comma separated value on this env variable:
```
SQS_SNS_VALID_TOPICS=service-a-topic,service-b-topic
```

 - D: Use the env variable if need a different value than `APP_ENV`:
 ```
SQS_SNS_ENV=local
```
This `SQS_SNS_ENV` allow us to have custom topics for each environment, if we for example generate new environments for test specific features, we can set here the feature name.

## Examples

### Event example

```PHP
<?php

namespace App\Events;

use App\Models\Order;
use Ralbear\EventsToSns\Contracts\ShouldBeInSns;
use Ralbear\EventsToSns\Traits\SendToSns;

class OrderCreatedEvent implements ShouldBeInSns
{
    use SendToSns;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function uniqueId()
    {
        return $this->order->id;
    }

    public function getTopic()
    {
        return 'service-a-topic';
    }
}
```

### Run worker

Run the worker:

```
$ php artisan queue:worker sqs-sns
```

### Job example

```PHP
<?php

namespace App\Jobs;

use Illuminate\Queue\Jobs\SqsJob;

class OrderCreatedJob
{
    public function handle(SqsJob $job, $data)
    {
        //Do something nice with your $data
        
        $job->delete();
    }
}
```

## Test

To run test:
```
$ composer test
```

## ToDo's
- Improve tests and coverage

## License

Laravel events to SNS is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
