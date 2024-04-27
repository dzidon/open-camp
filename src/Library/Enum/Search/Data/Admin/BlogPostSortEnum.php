<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin blog post sort cases.
 */
enum BlogPostSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'blogPost.createdAt DESC';
    case CREATED_AT_ASC = 'blogPost.createdAt ASC';
    case VIEW_COUNT_DESC = 'blogPostViewCount DESC';
    case VIEW_COUNT_ASC = 'blogPostViewCount ASC';
}
