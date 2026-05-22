<?php

namespace Database\Factories\Crm;

use App\Models\Crm\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 *
 * tenant_id is filled automatically by the BelongsToTenant trait from the
 * active TenantContext; pass ['tenant_id' => ...] to override in isolation.
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'name'            => $this->faker->name(),
            'company'         => $this->faker->company(),
            'email'           => $this->faker->unique()->safeEmail(),
            'phone'           => $this->faker->phoneNumber(),
            'source'          => $this->faker->randomElement(['web', 'referral', 'ads', 'manual', 'import']),
            'status'          => $this->faker->randomElement(['new', 'contacted', 'qualified', 'unqualified']),
            'score'           => $this->faker->numberBetween(0, 100),
            'estimated_value' => $this->faker->randomFloat(2, 500, 50000),
            'notes'           => $this->faker->optional()->sentence(),
        ];
    }
}
