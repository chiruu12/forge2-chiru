<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['open', 'pending', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return [
            'organization_id' => Organization::factory(),
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement($statuses),
            'priority' => fake()->randomElement($priorities),
            'requester_id' => User::factory(),
            'assignee_id' => null,
        ];
    }

    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }

    public function withRequester(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'requester_id' => $user->id,
        ]);
    }

    public function withAssignee(?User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'assignee_id' => $user?->id,
        ]);
    }
}
