<?php

namespace App\Enum\Search\Data\Admin;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * Admin user sort cases.
 */
enum UserSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'createdAt DESC';
    case CREATED_AT_ASC = 'createdAt ASC';
    case EMAIL_ASC = 'email ASC';
    case EMAIL_DESC = 'email DESC';
    case NAME_ASC = 'name ASC';
    case NAME_DESC = 'name DESC';
}
