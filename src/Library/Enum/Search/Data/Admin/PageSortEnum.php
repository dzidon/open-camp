<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin page sort cases.
 */
enum PageSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'page.createdAt DESC';
    case CREATED_AT_ASC = 'page.createdAt ASC';
}
