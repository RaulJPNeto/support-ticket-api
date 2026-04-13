<?php

namespace App\Queries;

use app\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;

class TicketQuery
{
    public function handle(User $user, array $filters)
    {
        $query = Ticket::query();

        if($user->role === UserRole::CLIENT) {
            $query->where('client_id', $user->id);

        } elseif($user->role === UserRole::AGENT) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_agent_id', $user->id)
                    ->orWhereNull('assigned_agent_id');
            });
        }

        $query->when($filters->status, fn($q) =>
        $q->where('status', $filters->status)
        );

        $query->when($filters->priority, fn($q) =>
        $q->where('priority', $filters->priority)
        );

        $filters->when($filters->category, fn($q) =>
        $q->where('category', $filters->category)
        );

        $filters->when($filters->search, function ($q) use ($filters) {
            $q->where('title', 'ilike', "%{$filters->search}%");
        });

        return $query;
    }
}
