<?php

namespace App\Policies\Crm;

use App\Models\Crm\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('crm.view');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->can('crm.view') && $user->tenant_id === $client->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->can('crm.create');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->can('crm.update') && $user->tenant_id === $client->tenant_id;
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->can('crm.delete') && $user->tenant_id === $client->tenant_id;
    }
}
