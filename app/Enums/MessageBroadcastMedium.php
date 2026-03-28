<?php

namespace App\Enums;

enum MessageBroadcastMedium : string
{
    case EMAIL = 'EMAIL';
    case SMS = 'SMS';
}
