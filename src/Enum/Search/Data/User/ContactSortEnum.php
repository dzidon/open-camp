<?php

namespace App\Enum\Search\Data\User;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * User contact sort cases.
 */
enum ContactSortEnum: string
{
    use SortEnumTrait;

    case NAME_ASC = 'name ASC';
    case NAME_DESC = 'name DESC';
}
