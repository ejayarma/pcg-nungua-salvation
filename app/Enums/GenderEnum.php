<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum GenderEnum: string
{
    use EnumValues;
    case MALE = 'MALE';
    case FEMALE = 'FEMALE';
}
