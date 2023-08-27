<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin camp date sort cases.
 */
enum CampDateSortEnum: string
{
    use SortEnumTrait;

    case START_AT_ASC = 'campDate.startAt ASC';
    case START_AT_DESC = 'campDate.startAt DESC';
    case PRICE_ASC = 'campDate.price ASC';
    case PRICE_DESC = 'campDate.price DESC';
    case CAPACITY_ASC = 'campDate.capacity ASC';
    case CAPACITY_DESC = 'campDate.capacity DESC';
}
