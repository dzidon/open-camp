<?php

namespace App\Library\Enum\Search\Data\User;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * User camp sort cases.
 */
enum CampSortEnum: string
{
    use SortEnumTrait;

    case PRIORITY_DESC = 'camp.priority DESC';
    case LOWEST_FULL_PRICE_ASC = 'lowestFullPrice ASC';
    case LOWEST_FULL_PRICE_DESC = 'lowestFullPrice DESC';
}
