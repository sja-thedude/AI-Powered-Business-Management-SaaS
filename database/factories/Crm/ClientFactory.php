<?php

namespace Database\Factories\Crm;

use App\Models\Crm\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name'    => $this->faker->name(),
            'company' => $this->faker->company(),
            'email'   => $this->faker->unique()->companyEmail(),
            'phone'   => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'status'  => $this->faker->randomElement(['active', 'prospect', 'inactive']),
            'tags'    => $this->faker->randomElements(['vip', 'enterprise', 'smb', 'partner'], 2),
        ];
    }
}
