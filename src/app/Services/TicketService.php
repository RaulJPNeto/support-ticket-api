<?php

namespace App\Services;

use App\Enums\TicketCategory;
use app\Enums\TicketPriority;
use app\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
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
            'client_id' => $user->id,
        ]);
    }

    public function update(Ticket $ticket, array $data, User $user): Ticket
    {
        $oldStatus = $ticket->status;

        $newStatus = isset($data['status'])
            ? TicketStatus::from($data['status'])
            : null;

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

        if ($newStatus && $newStatus != $oldStatus) {
            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by' => $user->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
            ]);
        }

        return $ticket->fresh();
    }

    public function delete(Ticket $ticket)
    {
        $ticket->delete();
    }

    public function restore(Ticket $ticket): Ticket
    {
        $ticket->restore();

        return $ticket->refresh();
    }

    public function forceDelete(Ticket $ticket): void
    {
        $ticket->forceDelete();
    }
}