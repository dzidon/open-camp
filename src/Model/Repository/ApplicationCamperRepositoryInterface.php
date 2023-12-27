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
}