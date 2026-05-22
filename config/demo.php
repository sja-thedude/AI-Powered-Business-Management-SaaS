<?php

/*
|--------------------------------------------------------------------------
| Demo / Seed Accounts
|--------------------------------------------------------------------------
|
| The accounts created by DatabaseSeeder for the demo workspace. Emails,
| names and the shared password are configurable via the SEED_* env vars so
| they can be changed per environment without touching code. Each account is
| assigned the named per-tenant role.
|
*/

return [

    'password' => env('SEED_DEMO_PASSWORD', 'password'),

    'workspace' => env('SEED_WORKSPACE_NAME', 'Acme Inc'),

    'accounts' => [
        [
            'role'     => 'Owner',
            'name'     => env('SEED_OWNER_NAME', 'Ava Owner'),
            'email'    => env('SEED_OWNER_EMAIL', 'owner@novabiz.test'),
            'position' => 'Owner',
        ],
        [
            'role'     => 'Admin',
            'name'     => env('SEED_ADMIN_NAME', 'Adam Admin'),
            'email'    => env('SEED_ADMIN_EMAIL', 'admin@novabiz.test'),
            'position' => 'Operations Lead',
        ],
        [
            'role'     => 'Member',
            'name'     => env('SEED_MEMBER_NAME', 'Mia Member'),
            'email'    => env('SEED_MEMBER_EMAIL', 'member@novabiz.test'),
            'position' => 'Account Executive',
        ],
    ],

];
