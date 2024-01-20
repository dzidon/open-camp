<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin sale config sort cases.
 */
enum DiscountConfigSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'discountConfig.createdAt DESC';
    case CREATED_AT_ASC = 'discountConfig.createdAt ASC';
    case NAME_ASC = 'discountConfig.name ASC';
    case NAME_DESC = 'discountConfig.name DESC';
}
