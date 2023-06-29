<?php

namespace App\Enum\Search\Data\Admin;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * Admin role sort cases.
 */
enum RoleSortEnum: string
{
    use SortEnumTrait;

    case ID_DESC = 'id DESC';
    case ID_ASC = 'id ASC';
}
