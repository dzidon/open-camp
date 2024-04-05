<?php

namespace App\Model\Library\CampDate;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\CampDate;

/**
 * Search result that contains camp dates and a number of pending applications for each camp date.
 */
interface AdminApplicationCampDatesResultInterface
{
    /**
     * @return PaginatorInterface
     */
    public function getPaginator(): PaginatorInterface;

    /**
     * @param string|CampDate $campDate
     * @return int|null If null is returned, the number of pending applications is not known for the given camp date.
     */
    public function getNumberOfPendingApplications(string|CampDate $campDate): ?int;

    /**
     * @param string|CampDate $campDate
     * @return int|null If null is returned, the number of accepted application campers is not known for the given camp date.
     */
    public function getNumberOfAcceptedApplicationCampers(string|CampDate $campDate): ?int;
}