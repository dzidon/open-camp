<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin application camper sort cases.
 */
enum ApplicationCamperSortEnum: string
{
    use SortEnumTrait;

    case CREATED_AT_DESC = 'applicationCamper.createdAt DESC';
    case CREATED_AT_ASC = 'applicationCamper.createdAt ASC';
    case BORN_AT_DESC = 'applicationCamper.bornAt DESC';
    case BORN_AT_ASC = 'applicationCamper.bornAt ASC';
}
