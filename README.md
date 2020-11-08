
## Laravel events to SNS

This library allow us to send Laravel events to an SNS topic.

## Install

```shell script
$ composer require ralbear/laravel-events-to-sns
```
## Configuration

### AWS credentials

If we use the same AWS account for SNS than for other AWS services on the application, we can use the default env keys for the credentials.

```
AWS_ACCESS_KEY_ID=<MAIN ACCESS KEY ID>
AWS_SECRET_ACCESS_KEY=<SECRECT ACCESS KEY>
AWS_DEFAULT_REGION=us-west-1
```
If we need specific credentials for SNS, use this env keys:

```
AWS_SNS_ACCESS_KEY_ID=<SNS ACCESS KEY ID>
AWS_SNS_SECRET_ACCESS_KEY=<SNS SECRET ACCESS KEY>
AWS_SNS_DEFAULT_REGION=eu-west-1
```

### Topics

The way this library is designed, define SNS topics based on three parts.

![ARN topic parts](https://raw.githubusercontent.com/ralbear/laravel-events-to-sns/docs/media/arn_topic_parts.png)
 
 - A: Use the env variable:
```
AWS_SNS_BASE_ARN=arn:aws:sns:eu-west-1:123456789
```
 - B: Defined in your event:
 ```PHP
public function getTopic(): string
{
    return 'service-a-topic';
}
```
The event level topics we use, should be previously defined on `config/events-to-sns.php`:
```
'topic' => [
    'valid' => [
        'service-a-topic',
        'service-b-topic'
    ],
```

 - D: Use the env variable:
 ```
AWS_SNS_TOPIC_POSTFIX=local
```
This `AWS_SNS_TOPIC_POSTFIX` allow us to have custom topics for each environment, if we for example generate new environments for test specific features, we can set here the feature name.

## Example

```PHP
<?php

namespace App\Events;

use App\Models\Order;
use Ralbear\EventsToSns\Contracts\ShouldBeInSns;
use Ralbear\EventsToSns\Traits\SendToSns;

class OrderCreatedEvent implements ShouldBeInSns
{
    use SendToSns;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getTopic(): string
    {
        return 'service-a-topic';
    }
}
```

## Test

To run test:
```
$ composer test
```

## License

Laravel events to SNS is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
