<?php

use Illuminate\Support\Facades\Broadcast;

// Private per-user channel (Laravel default — used for notifications).
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
 * Tenant-scoped private channels. A user may only subscribe to channels for
 * their own workspace, which keeps real-time data isolated per tenant.
 * Example: tenant.5.crm, tenant.5.notifications
 */
Broadcast::channel('tenant.{tenantId}.{stream}', function ($user, $tenantId, $stream) {
    return (int) $user->tenant_id === (int) $tenantId;
});
