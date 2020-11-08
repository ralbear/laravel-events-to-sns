<?php

return [
    'aws' => [
        'key' => env('AWS_SNS_ACCESS_KEY_ID') ?? env('AWS_ACCESS_KEY_ID') ?? '',
        'secret' => env('AWS_SNS_SECRET_ACCESS_KEY') ?? env('AWS_SECRET_ACCESS_KEY') ?? '',
        'region' => env('AWS_SNS_DEFAULT_REGION') ?? env('AWS_DEFAULT_REGION') ?? '',
        'base_ARN' => env('AWS_SNS_BASE_ARN') ?? ''
    ],
    'topic' => [
        'valid' => [
            'service-a-topic'
        ],
        'env_postfix' => env('AWS_SNS_TOPIC_POSTFIX') ?? '',
    ]
];
