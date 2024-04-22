<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin gallery sort cases.
 */
enum GalleryImageSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'galleryImage.createdAt DESC';
    case CREATED_AT_ASC = 'galleryImage.createdAt ASC';
}
