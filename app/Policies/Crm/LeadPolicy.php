<?php

namespace App\Policies\Crm;

use App\Models\Crm\Lead;
use App\Models\User;

/**
 * Lead authorization. Abilities map to the `crm.*` permission set; the tenant
 * Owner bypasses all checks via the Gate::before rule. A defensive
 * sameTenant() guard backs up the global TenantScope for cross-tenant safety.
 */
class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('crm.view');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('crm.view') && $this->sameTenant($user, $lead);
    }

    public function create(User $user): bool
    {
        return $user->can('crm.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can('crm.update') && $this->sameTenant($user, $lead);
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('crm.delete') && $this->sameTenant($user, $lead);
    }

    protected function sameTenant(User $user, Lead $lead): bool
    {
        return $user->tenant_id === $lead->tenant_id;
    }
}
