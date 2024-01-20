<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationCamper;

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
     * Returns the number of other complete (isDraft = false) and accepted applications that contain the given camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @return int
     */
    public function getNumberOfOtherCompleteAcceptedApplications(ApplicationCamper $applicationCamper): int;
}