<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin purchasable item variant sort cases.
 */
enum PurchasableItemVariantSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'purchasableItemVariant.createdAt DESC';
    case CREATED_AT_ASC = 'purchasableItemVariant.createdAt ASC';
    case NAME_ASC = 'purchasableItemVariant.name ASC';
    case NAME_DESC = 'purchasableItemVariant.name DESC';
    case PRIORITY_ASC = 'purchasableItemVariant.priority ASC';
    case PRIORITY_DESC = 'purchasableItemVariant.priority DESC';
}
