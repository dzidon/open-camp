<?php

namespace App\Enum\Search\Data\Admin;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * Admin user sort cases.
 */
enum UserSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'user.createdAt DESC';
    case CREATED_AT_ASC = 'user.createdAt ASC';
    case EMAIL_ASC = 'user.email ASC';
    case EMAIL_DESC = 'user.email DESC';
    case NAME_LAST_ASC = 'user.nameLast ASC';
    case NAME_LAST_DESC = 'user.nameLast DESC';
    case LAST_ACTIVE_AT_DESC = 'user.lastActiveAt DESC';
}
