<?php

return [
    'free' => [
        'name'             => 'Free',
        'images_per_month' => 10,
        'max_campaigns'    => 1,
        'ai_suggestions'   => false,
        'ai_review'        => false,
    ],
    'starter' => [
        'name'             => 'Starter',
        'images_per_month' => 50,
        'max_campaigns'    => 5,
        'ai_suggestions'   => true,
        'ai_review'        => false,
    ],
    'pro' => [
        'name'             => 'Pro',
        'images_per_month' => -1,
        'max_campaigns'    => -1,
        'ai_suggestions'   => true,
        'ai_review'        => true,
    ],
];
