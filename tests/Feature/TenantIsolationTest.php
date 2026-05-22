<?php

namespace Tests\Feature;

use App\Models\Crm\Lead;
use App\Services\TenantProvisioner;
use App\Support\TenantContext;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    private function provisionTenant(string $company, string $email): \App\Models\User
    {
        return app(TenantProvisioner::class)->provision(
            tenantData: ['name' => $company, 'plan' => 'growth'],
            ownerData: ['name' => 'Owner', 'email' => $email, 'password' => 'password'],
        );
    }

    public function test_a_tenant_cannot_see_another_tenants_leads(): void
    {
        $context = app(TenantContext::class);
        $registrar = app(PermissionRegistrar::class);

        $ownerA = $this->provisionTenant('Acme', 'a@acme.test');
        $ownerB = $this->provisionTenant('Globex', 'b@globex.test');

        // Seed one lead into each tenant within its own context.
        $context->run($ownerA->tenant, fn () => Lead::factory()->create(['name' => 'Acme Lead']));
        $context->run($ownerB->tenant, fn () => Lead::factory()->create(['name' => 'Globex Lead']));

        // Acting as tenant A's owner, only A's lead is visible.
        $context->set($ownerA->tenant);
        $registrar->setPermissionsTeamId($ownerA->tenant->id);

        $this->assertSame(1, Lead::count());
        $this->assertSame('Acme Lead', Lead::first()->name);
    }

    public function test_global_scope_applies_through_the_web_route(): void
    {
        $context = app(TenantContext::class);

        $ownerA = $this->provisionTenant('Acme', 'a@acme.test');
        $ownerB = $this->provisionTenant('Globex', 'b@globex.test');

        $context->run($ownerA->tenant, fn () => Lead::factory()->count(3)->create());
        $context->run($ownerB->tenant, fn () => Lead::factory()->count(5)->create());

        $response = $this->actingAs($ownerA)->get(route('crm.leads.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Crm/Leads/Index')
            ->where('leads.total', 3));
    }
}
