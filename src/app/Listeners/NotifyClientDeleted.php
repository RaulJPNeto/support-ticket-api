<?php

namespace App\Listeners;

use App\Events\TicketDeleted;
use App\Mail\TicketDeletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyClientDeleted
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
        Mail::to($event->ticket->client->email)
            ->send(new TicketDeletedMail($event->ticket, $event->deletedBy));
    }
}
