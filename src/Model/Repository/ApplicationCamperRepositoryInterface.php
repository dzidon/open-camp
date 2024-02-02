<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\CampDate;

interface ApplicationCamperRepositoryInterface
{
    /**
     * Saves an application camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @param bool $flush
     * @return void
     */
    public function saveApplicationCamper(ApplicationCamper $applicationCamper, bool $flush): void;

    /**
     * Removes an application camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @param bool $flush
     * @return void
     */
    public function removeApplicationCamper(ApplicationCamper $applicationCamper, bool $flush): void;

    /**
     * Finds application campers who occupy slots in the given camp date.
     *
     * @param CampDate $campDate
     * @return ApplicationCamper[]
     */
    public function findThoseThatOccupySlotsInCampDate(CampDate $campDate): array;

    /**
     * Returns the number of other complete (isDraft = false) and accepted applications that contain the given camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @return int
     */
    public function getNumberOfOtherCompleteAcceptedApplications(ApplicationCamper $applicationCamper): int;
}