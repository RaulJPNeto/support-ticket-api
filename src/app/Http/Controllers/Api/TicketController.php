<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketCategory;
use app\Enums\TicketPriority;
use app\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request)
    {
        $this->authorize('create', Ticket::class);
        $ticket = Ticket::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => TicketStatus::OPEN,
            'priority' => TicketPriority::from($request->input('priority')),
            'category' => TicketCategory::from($request->input('category')),
            'client_id' => $request->user()->id,
        ]);

        return response()->json([$ticket, 201]);
    }
}
