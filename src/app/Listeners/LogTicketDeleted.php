<?php

namespace App\Listeners;

use App\Events\TicketDeleted;
use Illuminate\Support\Facades\Log;

class LogTicketDeleted
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketDeleted $event): void
    {
        Log::warning('Ticket deleted', [
            'ticket_id' => $event->ticket->id,
            'title' => $event->ticket->title,
            'deleted_by' => $event->deletedBy->id,
        ]);
    }
}
