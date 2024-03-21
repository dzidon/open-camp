<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin application camp sort cases.
 */
enum ApplicationCampSortEnum: string
{
    use SortEnumTrait;

    case CAMP_NAME_ASC = 'camp.name ASC';
    case CAMP_NAME_DESC = 'camp.name DESC';
    case NUMBER_OF_PENDING_APPLICATIONS_DESC = 'numberOfPendingApplications DESC';
}
