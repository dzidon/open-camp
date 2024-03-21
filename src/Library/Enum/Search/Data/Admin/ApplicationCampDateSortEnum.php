<?php

namespace App\Library\Enum\Search\Data\Admin;

use App\Library\Enum\Search\Data\SortEnumTrait;

/**
 * Admin application camp date sort cases.
 */
enum ApplicationCampDateSortEnum: string
{
    use SortEnumTrait;

    case CAMP_DATE_START_AT_ASC = 'campDate.startAt ASC';
    case CAMP_DATE_START_AT_DESC = 'campDate.startAt DESC';
    case NUMBER_OF_PENDING_APPLICATIONS_DESC = 'numberOfPendingApplications DESC';
}
