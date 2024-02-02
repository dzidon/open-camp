<?php

namespace App\Model\Library\Camp;

use App\Model\Entity\CampDate;

/**
 * Contains camp dates of a camp with information about active camps.
 */
interface UserUpcomingCampDatesResultInterface
{
    /**
     * Returns available camp images of the given camp.
     *
     * @return CampDate[]
     */
    public function getCampDates(): array;

    /**
     * Returns true if the given camp date accepts applications.
     *
     * @param string|CampDate $campDate
     * @return bool
     */
    public function isCampDateOpen(string|CampDate $campDate): bool;
}