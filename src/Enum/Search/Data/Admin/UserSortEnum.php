<?php

namespace App\Enum\Search\Data\Admin;

use App\Enum\Search\Data\SortEnumTrait;

/**
 * Admin user sort cases.
 */
enum UserSortEnum: string
{
    use SortEnumTrait;

    case ID_DESC = 'id DESC';
    case ID_ASC = 'id ASC';
    case EMAIL_ASC = 'email ASC';
    case EMAIL_DESC = 'email DESC';
}
