<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin purchasable item sort cases.
 */
enum PurchasableItemSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'purchasableItem.createdAt DESC';
    case CREATED_AT_ASC = 'purchasableItem.createdAt ASC';
    case NAME_ASC = 'purchasableItem.name ASC';
    case NAME_DESC = 'purchasableItem.name DESC';
    case PRICE_ASC = 'purchasableItem.price ASC';
    case PRICE_DESC = 'purchasableItem.price DESC';
    case MAX_AMOUNT_PER_CAMPER_ASC = 'purchasableItem.maxAmountPerCamper ASC';
    case MAX_AMOUNT_PER_CAMPER_DESC = 'purchasableItem.maxAmountPerCamper DESC';
}
