<?php

namespace app\Enums;

enum UserRole: string
{
    case CLIENT = 'CLIENT';
    case AGENT = 'AGENT';
    case ADMIN = 'ADMIN';
}
