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
    case DEPOSIT_ASC = 'campDate.deposit ASC';
    case DEPOSIT_DESC = 'campDate.deposit DESC';
    case PRICE_WITHOUT_DEPOSIT_ASC = 'campDate.priceWithoutDeposit ASC';
    case PRICE_WITHOUT_DEPOSIT_DESC = 'campDate.priceWithoutDeposit DESC';
    case CAPACITY_ASC = 'campDate.capacity ASC';
    case CAPACITY_DESC = 'campDate.capacity DESC';
}
