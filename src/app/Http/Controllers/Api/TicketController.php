<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketCategory;
use app\Enums\TicketPriority;
use app\Enums\TicketStatus;
use app\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\IndexTicketRequest;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Queries\TicketQuery;
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


    public function index(IndexTicketRequest $request, TicketQuery $ticketQuery)
    {
        $query = $ticketQuery->handle(
            $request->user(),
            $request->validated()
        );
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
            $request->user()
        );

        return new TicketResource($ticket);
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $this->ticketService->delete($ticket, $request->user());

        return response()->noContent();
    }

    public function restore(Ticket $withTrash, int $id): TicketResource
    {
        $ticket = Ticket::withTrashed()->findOrFail($id);

        $this->authorize('restore', $ticket);

        $ticket = $this->ticketService->restore($ticket);

        return new TicketResource($ticket);
    }

    public function forceDelet(int $id)
    {
        $ticket = Ticket::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $ticket);

        $this->ticketService->forceDelete($ticket);

        return response()->noContent();
    }
}
