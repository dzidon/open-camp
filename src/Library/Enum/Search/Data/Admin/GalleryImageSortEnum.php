<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin gallery sort cases.
 */
enum GalleryImageSortEnum: string
{
    use SortEnumTrait;

    case PRIORITY_DESC = 'galleryImage.priority DESC';
    case PRIORITY_ASC = 'galleryImage.priority ASC';
}
