<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use App\Mail\TicketStatusChangedMail;
use Illuminate\Support\Facades\Mail;

class NotifyClientStatus
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
        Mail::to($event->ticket->client->email)
            ->send(new TicketStatusChangedMail($event->ticket, $event->oldStatus, $event->newStatus));
    }
}
