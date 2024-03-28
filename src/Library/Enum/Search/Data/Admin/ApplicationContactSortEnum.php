<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin application contact sort cases.
 */
enum ApplicationContactSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'applicationContact.createdAt DESC';
    case CREATED_AT_ASC = 'applicationContact.createdAt ASC';
    case NAME_LAST_ASC = 'applicationContact.nameLast ASC';
    case NAME_LAST_DESC = 'applicationContact.nameLast DESC';
}
