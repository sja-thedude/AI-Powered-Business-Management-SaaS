<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function __construct(protected TenantContext $context)
    {
    }

    public function index()
    {
        $tenant = $this->context->get();

        return Inertia::render('Settings/Index', [
            'workspace' => [
                'name'     => $tenant->name,
                'slug'     => $tenant->slug,
                'timezone' => $tenant->timezone,
                'currency' => $tenant->currency,
                'plan'     => $tenant->plan,
            ],
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function updateWorkspace(Request $request)
    {
        $this->authorize('settings.manage');

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'timezone'],
            'currency' => ['required', 'string', 'size:3'],
        ]);

        $this->context->get()->update($data);

        return back()->with('success', 'Workspace settings saved.');
    }

    public function team()
    {
        $users = User::with('roles:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'position', 'is_active', 'last_login_at']);

        return Inertia::render('Settings/Team', [
            'members' => $users,
            'roles'   => \Spatie\Permission\Models\Role::pluck('name'),
        ]);
    }

    public function audit()
    {
        $logs = AuditLog::with('user:id,name')
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Settings/Audit', [
            'logs' => $logs,
        ]);
    }
}
