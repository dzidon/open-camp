<?php

namespace App\Library\Enum\Search\Data\User;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * User profile application sort cases.
 */
enum ApplicationProfileSortEnum: string
{
    use SortEnumTrait;

    case COMPLETED_AT_DESC = 'application.completedAt DESC';
    case COMPLETED_AT_ASC = 'application.completedAt ASC';
}
