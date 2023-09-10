<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

enum TripLocationPathSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'tripLocationPath.createdAt DESC';
    case CREATED_AT_ASC = 'tripLocationPath.createdAt ASC';
    case NAME_ASC = 'tripLocationPath.name ASC';
    case NAME_DESC = 'tripLocationPath.name DESC';
}
