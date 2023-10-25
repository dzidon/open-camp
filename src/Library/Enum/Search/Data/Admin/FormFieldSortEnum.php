<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin form field sort cases.
 */
enum FormFieldSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'formField.createdAt DESC';
    case CREATED_AT_ASC = 'formField.createdAt ASC';
    case NAME_ASC = 'formField.name ASC';
    case NAME_DESC = 'formField.name DESC';
}
