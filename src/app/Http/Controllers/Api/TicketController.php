<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketCategory;
use app\Enums\TicketPriority;
use app\Enums\TicketStatus;
use app\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use AuthorizesRequests;
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }


    public function index(Request $request)
    {
        $user = $request->user();
        $query = Ticket::query();

        if($user->role === UserRole::CLIENT) {
            $query->where('client_id', $user->id);
        } elseif($user->role === UserRole::AGENT) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_agent_id', $user->id)
                    ->orWhereNull('assigned_agent_id');
            });
        } elseif($user->role === UserRole::ADMIN) {
            // sem filtro
        } else {
            abort(403);
        }

        $tickets = $query->latest()->paginate(10);

        return TicketResource::collection($tickets);
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', Ticket::class);

        return new TicketResource($ticket);
    }

    public function store(StoreTicketRequest $request)
    {
        $this->authorize('create', Ticket::class);

        $ticket = $this->ticketService->create(
            $request->validated(),
            $request->user()
        );

        return new TicketResource($ticket);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->authorize('update', Ticket::class);

        $ticket = $this->ticketService->update(
            $ticket,
            $request->validated(),
        );

        return new TicketResource($ticket);
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $this->ticketService->delete($ticket);

        return response()->noContent();

    }
}
