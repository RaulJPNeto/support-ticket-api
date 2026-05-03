<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use Illuminate\Support\Facades\Log;

class LogStatusChange
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
    public function handle(TicketStatusChanged $event): void
    {
        Log::info('Ticket status changed', [
            'ticket_id' => $event->ticket->id,
            'title' => $event->ticket->title,
            'old_status' => $event->oldStatus->value,
            'new_status' => $event->newStatus->value,
            'changed_by' => $event->changedBy->id,
        ]);
    }
}
