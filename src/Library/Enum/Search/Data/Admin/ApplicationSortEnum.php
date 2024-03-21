<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin application sort cases.
 */
enum ApplicationSortEnum: string
{
    use SortEnumTrait;

    case COMPLETED_AT_DESC = 'application.completedAt DESC';
    case COMPLETED_AT_ASC = 'application.completedAt ASC';
}
