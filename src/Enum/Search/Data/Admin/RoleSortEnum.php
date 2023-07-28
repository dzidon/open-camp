<?php

namespace App\Enum\Search\Data\Admin;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * Admin role sort cases.
 */
enum RoleSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'createdAt DESC';
    case CREATED_AT_ASC = 'createdAt ASC';
    case LABEL_ASC = 'label ASC';
    case LABEL_DESC = 'label DESC';
}
