<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Enums\UserRole;
use App\Enums\SupportLevel;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketCategory;
use App\Enums\CommentVisibility;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuários fixos para usar no Bruno
        $admin = User::factory()->admin()->create([
            'name'  => 'Admin User',
            'email' => 'admin@test.com',
        ]);

        $agents = User::factory()->agent()->count(3)->create();

        $clients = User::factory()->client()->count(10)->create();

        // Tickets abertos sem agente
        Ticket::factory()
            ->open()
            ->count(10)
            ->recycle($clients)
            ->create();

        // Tickets em progresso com agente atribuído
        foreach ($agents as $agent) {
            Ticket::factory()
                ->inProgress($agent)
                ->count(5)
                ->recycle($clients)
                ->create();
        }

        // Tickets variados (status/prioridade aleatórios)
        Ticket::factory()
            ->count(20)
            ->recycle($clients)
            ->recycle($agents)
            ->create();

        // Comentários nos tickets
        $tickets = Ticket::all();

        foreach ($tickets as $ticket) {
            // Comentário público do cliente
            TicketComment::factory()
                ->public()
                ->create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $ticket->client_id,
                ]);

            // Comentário interno do agente (se tiver agente)
            if ($ticket->assigned_agent_id) {
                TicketComment::factory()
                    ->internal()
                    ->create([
                        'ticket_id' => $ticket->id,
                        'user_id'   => $ticket->assigned_agent_id,
                    ]);
            }
        }
    }
}
