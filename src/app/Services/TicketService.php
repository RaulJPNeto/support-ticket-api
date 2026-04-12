<?php

namespace App\Services;

use App\Enums\TicketCategory;
use app\Enums\TicketPriority;
use app\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketService
{
    public function create(array $data, User $user): Ticket
    {
        return Ticket::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => TicketStatus::OPEN,
            'priority' => TicketPriority::from($data['priority']),
            'category' => TicketCategory::from($data['category']),
            'client_id' =>$user->id,
        ]);
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        $ticket->update([
            'title' => $data['title'] ?? $ticket->title,
            'description' => $data['description'] ?? $ticket->description,
            'priority' => isset($data['priority'])
                ? TicketPriority::from($data['priority'])
                : $ticket->priority,
            'category' => isset($data['category'])
                ? TicketCategory::from($data['category'])
                : $ticket->category,
        ]);

        return $ticket;
    }

    public function delete(Ticket $ticket)
    {
        $ticket->delete();
    }
}
