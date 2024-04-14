<?php

namespace App\Library\Enum\Search\Data\User;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin role sort cases.
 */
enum BlogPostSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'blogPost.createdAt DESC';
    case CREATED_AT_ASC = 'blogPost.createdAt ASC';
}
