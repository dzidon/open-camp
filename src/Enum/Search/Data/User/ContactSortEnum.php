<?php

namespace App\Enum\Search\Data\User;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * User contact sort cases.
 */
enum ContactSortEnum: string
{
    use SortEnumTrait;

    case ID_DESC = 'id DESC';
    case ID_ASC = 'id ASC';
}
