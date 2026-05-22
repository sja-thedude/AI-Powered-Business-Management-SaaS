<?php

namespace Tests\Feature;

use App\Models\Crm\Lead;
use App\Services\TenantProvisioner;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmModuleTest extends TestCase
{
    use RefreshDatabase;

    private \App\Models\User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        $this->owner = app(TenantProvisioner::class)->provision(
            tenantData: ['name' => 'Acme', 'plan' => 'growth'],
            ownerData: ['name' => 'Owner', 'email' => 'owner@acme.test', 'password' => 'password'],
        );
    }

    public function test_owner_can_create_a_lead_via_web(): void
    {
        $response = $this->actingAs($this->owner)->post(route('crm.leads.store'), [
            'name'   => 'Jane Prospect',
            'source' => 'web',
            'status' => 'new',
        ]);

        $response->assertRedirect(route('crm.leads.index'));
        $this->assertDatabaseHas('leads', [
            'name'      => 'Jane Prospect',
            'tenant_id' => $this->owner->tenant_id,
        ]);
    }

    public function test_lead_can_be_managed_through_the_api(): void
    {
        $token = $this->owner->createToken('test')->plainTextToken;

        $create = $this->withToken($token)->postJson('/api/v1/crm/leads', [
            'name'   => 'API Lead',
            'source' => 'manual',
            'status' => 'new',
        ]);
        $create->assertCreated()->assertJsonPath('data.name', 'API Lead');

        $list = $this->withToken($token)->getJson('/api/v1/crm/leads');
        $list->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_unauthenticated_api_requests_are_rejected(): void
    {
        $this->getJson('/api/v1/crm/leads')->assertUnauthorized();
    }

    public function test_lead_conversion_creates_a_client(): void
    {
        $lead = null;
        app(\App\Support\TenantContext::class)->run($this->owner->tenant, function () use (&$lead) {
            $lead = Lead::factory()->create(['status' => 'qualified']);
        });

        $this->actingAs($this->owner)->post(route('crm.leads.convert', $lead->id));

        $this->assertDatabaseHas('clients', ['name' => $lead->name, 'tenant_id' => $this->owner->tenant_id]);
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'converted']);
    }
}
