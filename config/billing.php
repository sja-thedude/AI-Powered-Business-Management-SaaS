<?php

/*
|--------------------------------------------------------------------------
| Subscription Plans
|--------------------------------------------------------------------------
|
| The catalog of SaaS plans. `tier` drives module gating (see config/modules
| min_plan) and `stripe_price` is the Stripe Price ID Cashier subscribes to.
| `limits` are enforced by App\Support\PlanGate (seats, records, AI credits).
|
| Tier ordering is used for "min_plan" comparisons: starter < growth < scale.
|
*/

return [

    'trial_days' => 14,

    'tier_order' => ['starter', 'growth', 'scale'],

    'plans' => [

        'starter' => [
            'name' => 'Starter',
            'tier' => 'starter',
            'price' => 29,
            'interval' => 'month',
            'stripe_price' => env('STRIPE_PRICE_STARTER', 'price_starter'),
            'description' => 'For small teams getting their operations online.',
            'limits' => [
                'seats' => 5,
                'ai_credits' => 500,
                'storage_gb' => 5,
            ],
            'features' => [
                'CRM, Invoicing & Projects',
                'Up to 5 team members',
                'Real-time notifications',
                'Email support',
            ],
        ],

        'growth' => [
            'name' => 'Growth',
            'tier' => 'growth',
            'price' => 99,
            'interval' => 'month',
            'stripe_price' => env('STRIPE_PRICE_GROWTH', 'price_growth'),
            'description' => 'For scaling companies running the full back office.',
            'limits' => [
                'seats' => 25,
                'ai_credits' => 5000,
                'storage_gb' => 50,
            ],
            'features' => [
                'Everything in Starter',
                'HR, Payroll, Attendance & Inventory',
                'Client Portal',
                'AI sales prediction & analytics',
                'Priority support',
            ],
        ],

        'scale' => [
            'name' => 'Scale',
            'tier' => 'scale',
            'price' => 299,
            'interval' => 'month',
            'stripe_price' => env('STRIPE_PRICE_SCALE', 'price_scale'),
            'description' => 'For enterprises that need it all, unmetered.',
            'limits' => [
                'seats' => 0, // 0 = unlimited
                'ai_credits' => 0,
                'storage_gb' => 500,
            ],
            'features' => [
                'Everything in Growth',
                'Unlimited team members',
                'Unlimited AI credits',
                'Dedicated success manager',
                'SSO & audit exports',
            ],
        ],

    ],

];
