<?php

namespace App\Enum;

enum UserStatusEnum: string
{
    case ACTIVE = 'Active';
    case SUSPENDED = 'Suspended';
}
