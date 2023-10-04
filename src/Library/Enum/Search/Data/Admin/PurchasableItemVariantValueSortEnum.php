<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin purchasable item variant value sort cases.
 */
enum PurchasableItemVariantValueSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'purchasableItemVariantValue.createdAt DESC';
    case CREATED_AT_ASC = 'purchasableItemVariantValue.createdAt ASC';
    case NAME_ASC = 'purchasableItemVariantValue.name ASC';
    case NAME_DESC = 'purchasableItemVariantValue.name DESC';
    case PRIORITY_ASC = 'purchasableItemVariantValue.priority ASC';
    case PRIORITY_DESC = 'purchasableItemVariantValue.priority DESC';
}
