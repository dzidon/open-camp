<?php

namespace App\Enum\Search\Data\User;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * User camper sort cases.
 */
enum CamperSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'camper.createdAt DESC';
    case CREATED_AT_ASC = 'camper.createdAt ASC';
}
