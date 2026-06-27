<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        Ticket::truncate();

        $orgA = Organization::where('slug', 'org-a')->first();
        $orgB = Organization::where('slug', 'org-b')->first();

        $orgAUsers = User::where('organization_id', $orgA->id)->get();
        $orgBUsers = User::where('organization_id', $orgB->id)->get();

        $orgAAdmin = $orgAUsers->first(fn ($u) => $u->role === 'admin');
        $orgAAgents = $orgAUsers->where('role', 'agent')->values();
        $orgACustomers = $orgAUsers->where('role', 'customer')->values();

        $orgBAdmin = $orgBUsers->first(fn ($u) => $u->role === 'admin');
        $orgBAgents = $orgBUsers->where('role', 'agent')->values();
        $orgBCustomers = $orgBUsers->where('role', 'customer')->values();

        // --- Org A tickets (~12) ---
        // Customer 1A has 3+ tickets (for "customer sees only their own" test)
        $this->createTicket($orgA, $orgACustomers[0], $orgAAgents[0], 'open', 'high');
        $this->createTicket($orgA, $orgACustomers[0], $orgAAgents[0], 'pending', 'medium');
        $this->createTicket($orgA, $orgACustomers[0], null, 'resolved', 'low');
        $this->createTicket($orgA, $orgACustomers[0], null, 'closed', 'urgent');

        // Customer 2A has 2 tickets
        $this->createTicket($orgA, $orgACustomers[1], $orgAAgents[1], 'open', 'low');
        $this->createTicket($orgA, $orgACustomers[1], null, 'pending', 'high');

        // Agent-created tickets
        $this->createTicket($orgA, $orgAAgents[0], $orgAAgents[1], 'open', 'medium');
        $this->createTicket($orgA, $orgAAgents[1], $orgAAgents[0], 'resolved', 'urgent');

        // Admin-created tickets
        $this->createTicket($orgA, $orgAAdmin, $orgAAgents[0], 'closed', 'low');
        $this->createTicket($orgA, $orgAAdmin, null, 'open', 'high');
        $this->createTicket($orgA, $orgAAdmin, $orgAAgents[1], 'pending', 'medium');
        $this->createTicket($orgA, $orgAAdmin, null, 'resolved', 'urgent');

        // --- Org B tickets (~12) ---
        $this->createTicket($orgB, $orgBCustomers[0], $orgBAgents[0], 'open', 'high');
        $this->createTicket($orgB, $orgBCustomers[0], $orgBAgents[0], 'pending', 'medium');
        $this->createTicket($orgB, $orgBCustomers[0], null, 'resolved', 'low');
        $this->createTicket($orgB, $orgBCustomers[0], null, 'closed', 'urgent');
        $this->createTicket($orgB, $orgBCustomers[1], $orgBAgents[1], 'open', 'low');
        $this->createTicket($orgB, $orgBCustomers[1], null, 'pending', 'high');
        $this->createTicket($orgB, $orgBAgents[0], $orgBAgents[1], 'open', 'medium');
        $this->createTicket($orgB, $orgBAgents[1], $orgBAgents[0], 'resolved', 'urgent');
        $this->createTicket($orgB, $orgBAdmin, $orgBAgents[0], 'closed', 'low');
        $this->createTicket($orgB, $orgBAdmin, null, 'open', 'high');
        $this->createTicket($orgB, $orgBAdmin, $orgBAgents[1], 'pending', 'medium');
        $this->createTicket($orgB, $orgBAdmin, null, 'resolved', 'urgent');
    }

    private function createTicket(
        Organization $organization,
        User $requester,
        ?User $assignee,
        string $status,
        string $priority
    ): void {
        Ticket::create([
            'organization_id' => $organization->id,
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => $status,
            'priority' => $priority,
            'requester_id' => $requester->id,
            'assignee_id' => $assignee?->id,
        ]);
    }
}
