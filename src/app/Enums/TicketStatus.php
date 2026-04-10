<?php

namespace app\Enums;

enum TicketStatus: string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case WAITING_CUSTOMER = 'WAITING_CUSTOMER';
    case RESOLVED = 'RESOLVED';
    case CLOSED = 'CLOSED';
    case CANCELLED = 'CANCELLED';
}
