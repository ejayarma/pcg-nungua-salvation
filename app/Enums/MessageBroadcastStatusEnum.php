<?php

namespace App\Enums;

enum MessageBroadcastStatusEnum :  string
{
    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case FAILED = 'FAILED';
}
