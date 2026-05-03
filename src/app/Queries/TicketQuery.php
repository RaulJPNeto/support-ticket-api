<?php

namespace App\Queries;

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;

class TicketQuery
{
    public function handle(User $user, array $filters)
    {
        $query = Ticket::query();

        if ($user->role === UserRole::CLIENT) {
            $query->where('client_id', $user->id);
        } elseif ($user->role === UserRole::AGENT) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_agent_id', $user->id)
                    ->orWhereNull('assigned_agent_id');
            });
        }

        $query->when(
            $filters['status'] ?? null,
            fn ($q) => $q->where('status', $filters['status'])
        );

        $query->when(
            $filters['priority'] ?? null,
            fn ($q) => $q->where('priority', $filters['priority'])
        );

        $query->when(
            $filters['category'] ?? null,
            fn ($q) => $q->where('category', $filters['category'])
        );

        $query->when(
            $filters['search'] ?? null,
            fn ($q) => $q->where('title', 'ilike', "%{$filters['search']}%")
        );

        return $query;
    }
}
