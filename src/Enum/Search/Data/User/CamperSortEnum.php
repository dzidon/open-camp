<?php

namespace App\Enum\Search\Data\User;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * User camper sort cases.
 */
enum CamperSortEnum: string
{
    use SortEnumTrait;

    case ID_DESC = 'id DESC';
    case ID_ASC = 'id ASC';
}
