<?php

namespace Database\Factories;

use App\Enums\TicketCategory;
use app\Enums\TicketPriority;
use app\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'               => fake()->sentence(6),
            'description'         => fake()->paragraph(3),
            'status'              => fake()->randomElement(TicketStatus::cases()),
            'priority'            => fake()->randomElement(TicketPriority::cases()),
            'category'            => fake()->randomElement(TicketCategory::cases()),
            'client_id'           => User::factory()->client(),
            'assigned_agent_id'   => null,
        ];
    }

    public function assigned(User $agent): static
    {
        return $this->state([
            'assigned_agent_id' => $agent->id,
        ]);
    }

    public function open(): static
    {
        return $this->state(['status' => TicketStatus::OPEN]);
    }

    public function inProgress(User $agent): static
    {
        return $this->state([
            'status'            => TicketStatus::IN_PROGRESS,
            'assigned_agent_id' => $agent->id,
        ]);
    }
}
