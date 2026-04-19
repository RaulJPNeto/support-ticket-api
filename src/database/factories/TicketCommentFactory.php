<?php

namespace Database\Factories;

use app\Enums\CommentVisibility;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketComment>
 */
class TicketCommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_id'  => Ticket::factory(),
            'user_id'    => User::factory(),
            'content'    => fake()->paragraph(2),
            'visibility' => fake()->randomElement(CommentVisibility::cases()),
        ];
    }

    public function public(): static
    {
        return $this->state(['visibility' => CommentVisibility::PUBLIC]);
    }

    public function internal(): static
    {
        return $this->state(['visibility' => CommentVisibility::INTERNAL]);
    }
}
