<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin attachment config sort cases.
 */
enum AttachmentConfigSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'attachmentConfig.createdAt DESC';
    case CREATED_AT_ASC = 'attachmentConfig.createdAt ASC';
    case NAME_ASC = 'attachmentConfig.name ASC';
    case NAME_DESC = 'attachmentConfig.name DESC';
    case MAX_SIZE_ASC = 'attachmentConfig.maxSize ASC';
    case MAX_SIZE_DESC = 'attachmentConfig.maxSize DESC';
}
