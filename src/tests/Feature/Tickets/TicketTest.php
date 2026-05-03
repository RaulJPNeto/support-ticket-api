<?php

namespace Tests\Feature\Tickets;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    private function createClient(): User
    {
        return User::factory()->client()->create();
    }

    private function createAgent(): User
    {
        return User::factory()->agent()->create();
    }

    private function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    private function createTicket(User $client, array $attributes = []): Ticket
    {
        return Ticket::factory()->create(array_merge([
            'client_id' => $client->id,
        ], $attributes));
    }

    private function ticketPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Erro no sistema',
            'description' => 'Descrição do problema',
            'priority' => TicketPriority::HIGH->value,
            'category' => TicketCategory::BUG->value,
        ], $overrides);
    }

    // INDEX
    public function test_client_can_list_own_tickets(): void
    {
        $client = $this->createClient();
        $this->createTicket($client);
        $this->createTicket($this->createClient()); // ticket de outro cliente

        $response = $this->actingAs($client)->getJson('/api/tickets');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_admin_can_list_all_tickets(): void
    {
        $admin = $this->createAdmin();
        $this->createTicket($this->createClient());
        $this->createTicket($this->createClient());

        $response = $this->actingAs($admin)->getJson('/api/tickets');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_unauthenticated_user_cannot_list_tickets(): void
    {
        $response = $this->getJson('/api/tickets');

        $response->assertStatus(401);
    }

    // SHOW
    public function test_client_can_view_own_ticket(): void
    {
        $client = $this->createClient();
        $ticket = $this->createTicket($client);

        $response = $this->actingAs($client)->getJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $ticket->id]);
    }

    public function test_client_cannot_view_other_client_ticket(): void
    {
        $client = $this->createClient();
        $ticket = $this->createTicket($this->createClient());

        $response = $this->actingAs($client)->getJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(403);
    }

    // STORE
    public function test_client_can_create_ticket(): void
    {
        $client = $this->createClient();

        $response = $this->actingAs($client)
            ->postJson('/api/tickets', $this->ticketPayload());

        $response->assertStatus(201);
        $response->assertJsonFragment(['title' => 'Erro no sistema']);
    }

    public function test_agent_cannot_create_ticket(): void
    {
        $agent = $this->createAgent();

        $response = $this->actingAs($agent)
            ->postJson('/api/tickets', $this->ticketPayload());

        $response->assertStatus(403);
    }

    public function test_client_cannot_create_ticket_with_invalid_data(): void
    {
        $client = $this->createClient();

        $response = $this->actingAs($client)
            ->postJson('/api/tickets', [
                'title' => '',
                'priority' => 'INVALIDO',
            ]);

        $response->assertStatus(422);
    }

    // UPDATE
    public function test_admin_can_update_ticket_status(): void
    {
        $admin = $this->createAdmin();
        $ticket = $this->createTicket($this->createClient(), [
            'status' => TicketStatus::OPEN,
        ]);

        $response = $this->actingAs($admin)
            ->putJson("/api/tickets/{$ticket->id}", [
                'status' => TicketStatus::IN_PROGRESS->value,
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => TicketStatus::IN_PROGRESS->value]);
    }

    public function test_status_change_records_history(): void
    {
        $admin = $this->createAdmin();
        $ticket = $this->createTicket($this->createClient(), [
            'status' => TicketStatus::OPEN,
        ]);

        $this->actingAs($admin)
            ->putJson("/api/tickets/{$ticket->id}", [
                'status' => TicketStatus::IN_PROGRESS->value,
            ]);

        $this->assertDatabaseHas('ticket_status_histories', [
            'ticket_id' => $ticket->id,
            'from_status' => TicketStatus::OPEN->value,
            'to_status' => TicketStatus::IN_PROGRESS->value,
        ]);
    }

    public function test_client_cannot_update_other_client_ticket(): void
    {
        $client = $this->createClient();
        $ticket = $this->createTicket($this->createClient());

        $response = $this->actingAs($client)
            ->putJson("/api/tickets/{$ticket->id}", [
                'title' => 'Tentativa de alteração',
            ]);

        $response->assertStatus(403);
    }

    // DELETE
    public function test_admin_can_soft_delete_ticket(): void
    {
        $admin = $this->createAdmin();
        $ticket = $this->createTicket($this->createClient());

        $response = $this->actingAs($admin)
            ->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    public function test_client_cannot_delete_ticket(): void
    {
        $client = $this->createClient();
        $ticket = $this->createTicket($client);

        $response = $this->actingAs($client)
            ->deleteJson("/api/tickets/{$ticket->id}");

        $response->assertStatus(403);
    }

    // RESTORE
    public function test_admin_can_restore_ticket(): void
    {
        $admin = $this->createAdmin();
        $ticket = $this->createTicket($this->createClient());
        $ticket->delete();

        $response = $this->actingAs($admin)
            ->postJson("/api/tickets/{$ticket->id}/restore");

        $response->assertStatus(200);
        $this->assertNotSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    // FORCE DELETE
    public function test_admin_can_force_delete_ticket(): void
    {
        $admin = $this->createAdmin();
        $ticket = $this->createTicket($this->createClient());
        $ticket->delete();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/tickets/{$ticket->id}/force-delete");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }
}
