<?php

namespace App\Enum\Search\Data\Admin;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * Admin camp sort cases.
 */
enum CampSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'camp.createdAt DESC';
    case CREATED_AT_ASC = 'camp.createdAt ASC';
    case NAME_ASC = 'camp.name ASC';
    case NAME_DESC = 'camp.name DESC';
    case URL_NAME_ASC = 'camp.urlName ASC';
    case URL_NAME_DESC = 'camp.urlName DESC';
}
