<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusEnum: string
{
    case sent = "sent";
    case read = "read";
}
