<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin downloadable file sort cases.
 */
enum DownloadableFileSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'downloadableFile.createdAt DESC';
    case CREATED_AT_ASC = 'downloadableFile.createdAt ASC';
    case TITLE_ASC = 'downloadableFile.title ASC';
    case TITLE_DESC = 'downloadableFile.title DESC';
    case PRIORITY_DESC = 'downloadableFile.priority DESC';
    case PRIORITY_ASC = 'downloadableFile.priority ASC';
}
