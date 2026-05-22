<?php

namespace App\Policies\Crm;

use App\Models\Crm\Deal;
use App\Models\User;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('crm.view');
    }

    public function view(User $user, Deal $deal): bool
    {
        return $user->can('crm.view') && $user->tenant_id === $deal->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->can('crm.create');
    }

    public function update(User $user, Deal $deal): bool
    {
        return $user->can('crm.update') && $user->tenant_id === $deal->tenant_id;
    }

    public function delete(User $user, Deal $deal): bool
    {
        return $user->can('crm.delete') && $user->tenant_id === $deal->tenant_id;
    }
}
