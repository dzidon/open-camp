<?php

namespace App\Enum\Search\Data\User;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * User contact sort cases.
 */
enum ContactSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'contact.createdAt DESC';
    case CREATED_AT_ASC = 'contact.createdAt ASC';
}
