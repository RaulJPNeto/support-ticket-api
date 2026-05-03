<?php

namespace App\Providers;

use App\Events\TicketDeleted;
use App\Events\TicketStatusChanged;
use App\Listeners\LogStatusChange;
use App\Listeners\LogTicketDeleted;
use App\Listeners\NotifyClientDeleted;
use App\Listeners\NotifyClientStatus;
use App\Models\Ticket;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Ticket::class => TicketPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            TicketStatusChanged::class,
            [LogStatusChange::class, 'handle']
        );

        Event::listen(
            TicketStatusChanged::class,
            [NotifyClientStatus::class, 'handle']
        );

        Event::listen(
            TicketDeleted::class,
            [LogTicketDeleted::class, 'handle']
        );

        Event::listen(
            TicketDeleted::class,
            [NotifyClientDeleted::class, 'handle']
        );
    }
}
