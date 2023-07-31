<?php

namespace App\Enum\Search\Data\Admin;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * Admin role sort cases.
 */
enum RoleSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'role.createdAt DESC';
    case CREATED_AT_ASC = 'role.createdAt ASC';
    case LABEL_ASC = 'role.label ASC';
    case LABEL_DESC = 'role.label DESC';
}
