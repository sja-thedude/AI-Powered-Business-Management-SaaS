<?php

namespace Database\Seeders;

use App\Models\Crm\Activity;
use App\Models\Crm\Client;
use App\Models\Crm\Deal;
use App\Models\Crm\Lead;
use App\Models\User;
use App\Services\Crm\PipelineFactory;
use App\Services\TenantProvisioner;
use App\Support\TenantContext;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Global permission catalog.
        $this->call(PermissionSeeder::class);

        // Demo accounts (roles/emails/password) come from config/demo.php,
        // which is driven by SEED_* env vars.
        $password = Hash::make(config('demo.password'));
        $accounts = collect(config('demo.accounts'));
        $ownerAccount = $accounts->firstWhere('role', 'Owner');

        // 2. A demo workspace with its Owner (provisioner sets tenant context).
        $owner = app(TenantProvisioner::class)->provision(
            tenantData: ['name' => config('demo.workspace'), 'plan' => 'growth'],
            ownerData: [
                'name'     => $ownerAccount['name'],
                'email'    => $ownerAccount['email'],
                'password' => $password,
            ],
        );

        $tenant = $owner->tenant;
        app(TenantContext::class)->set($tenant);
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);

        $this->command->info("Seeded tenant #{$tenant->id} ({$tenant->name}). Login: {$ownerAccount['email']} / ".config('demo.password'));

        // 3. The rest of the team (every non-Owner account in config/demo.php).
        $team = [$owner->id];

        foreach ($accounts->where('role', '!=', 'Owner') as $account) {
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name'      => $account['name'],
                'email'     => $account['email'],
                'password'  => $password,
                'position'  => $account['position'],
            ]);
            $user->assignRole($account['role']);
            $team[] = $user->id;
        }

        // 4. CRM demo data.
        $pipeline = app(PipelineFactory::class)->defaultPipeline();
        $stages = $pipeline->stages;

        Lead::factory(25)->make()->each(function (Lead $lead) use ($team) {
            $lead->owner_id = fake()->randomElement($team);
            $lead->save();
        });

        $clients = Client::factory(15)->make()->each(function (Client $client) use ($team) {
            $client->owner_id = fake()->randomElement($team);
            $client->save();
        });

        // Spread deals across stages, with some already won/lost for analytics.
        for ($i = 0; $i < 30; $i++) {
            $stage = $stages->random();
            $status = fake()->randomElement(['open', 'open', 'open', 'won', 'lost']);

            Deal::create([
                'pipeline_id'         => $pipeline->id,
                'pipeline_stage_id'   => $stage->id,
                'client_id'           => $clients->random()->id,
                'owner_id'            => fake()->randomElement($team),
                'title'               => fake()->catchPhrase(),
                'value'               => fake()->randomFloat(2, 1000, 80000),
                'currency'            => 'USD',
                'status'              => $status,
                'probability'        => $stage->probability,
                'position'            => $i,
                'expected_close_date' => fake()->dateTimeBetween('now', '+3 months'),
                'closed_at'           => $status === 'open' ? null : fake()->dateTimeBetween('-4 months', 'now'),
            ]);
        }

        // 5. A few activities on random clients (communication history).
        Client::all()->random(8)->each(function (Client $client) use ($team) {
            Activity::create([
                'user_id'      => fake()->randomElement($team),
                'subject_type' => Client::class,
                'subject_id'   => $client->id,
                'type'         => fake()->randomElement(['note', 'call', 'email', 'meeting']),
                'title'        => fake()->sentence(4),
                'body'         => fake()->paragraph(),
                'completed_at' => now(),
            ]);
        });
    }
}
