<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

enum TripLocationSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'tripLocation.createdAt DESC';
    case CREATED_AT_ASC = 'tripLocation.createdAt ASC';
    case NAME_ASC = 'tripLocation.name ASC';
    case NAME_DESC = 'tripLocation.name DESC';
    case PRICE_ASC = 'tripLocation.price ASC';
    case PRICE_DESC = 'tripLocation.price DESC';
    case PRIORITY_ASC = 'tripLocation.priority ASC';
    case PRIORITY_DESC = 'tripLocation.priority DESC';
}
