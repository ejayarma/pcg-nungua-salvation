<?php

namespace App\Enums;

enum MessageBroadcastRecipientEnum : string
{
    case ALL = 'ALL';
    case GENERATIONAL_GROUP = 'GENERATIONAL_GROUP';
    case CUSTOM = 'CUSTOM';
}
