<?php

namespace App\Policies\Crm;

use App\Models\User;

class ActivityPolicy
{
    public function create(User $user): bool
    {
        return $user->can('crm.update');
    }

    public function delete(User $user): bool
    {
        return $user->can('crm.delete');
    }
}
