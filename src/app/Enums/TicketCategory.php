<?php

namespace App\Enums;

enum TicketCategory: string
{
    case INCIDENT = 'INCIDENT';
    case ACCESS = 'ACCESS';
    case BUG = 'BUG';
    case FEATURE_REQUEST = 'FEATURE_REQUEST';
    case INFRASTRUCTURE = 'INFRASTRUCTURE';
    case OTHER = 'OTHER';
}
