<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin application admin attachment sort cases.
 */
enum ApplicationAdminAttachmentSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'applicationAdminAttachment.createdAt DESC';
    case CREATED_AT_ASC = 'applicationAdminAttachment.createdAt ASC';
    case LABEL_ASC = 'applicationAdminAttachment.label ASC';
    case LABEL_DESC = 'applicationAdminAttachment.label DESC';
}
