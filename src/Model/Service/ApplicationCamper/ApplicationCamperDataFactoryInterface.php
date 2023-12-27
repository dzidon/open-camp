<?php

namespace App\Model\Service\ApplicationCamper;

use App\Library\Data\User\ApplicationCamperData;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;

/**
 * Creates application camper data.
 */
interface ApplicationCamperDataFactoryInterface
{
    /**
     * Creates application camper data from a camp date.
     *
     * @param CampDate $campDate
     * @return ApplicationCamperData
     */
    public function createApplicationCamperDataFromCampDate(CampDate $campDate): ApplicationCamperData;

    /**
     * Creates application camper data from an application.
     *
     * @param Application $application
     * @return ApplicationCamperData
     */
    public function createApplicationCamperDataFromApplication(Application $application): ApplicationCamperData;
}