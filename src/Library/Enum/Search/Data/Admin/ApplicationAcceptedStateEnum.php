<?php

namespace App\Library\Enum\Search\Data\Admin;

/**
 * Admin application accepted state cases.
 */
enum ApplicationAcceptedStateEnum: string
{
    case UNSETTLED = 'NULL';
    case ACCEPTED = 'TRUE';
    case DECLINED = 'FALSE';
}
