<?php

namespace App\Model\Service\ApplicationCamper;

use App\Library\Data\Common\ApplicationCamperData;
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
     * @return \App\Library\Data\Common\ApplicationCamperData
     */
    public function createApplicationCamperDataFromCampDate(CampDate $campDate): ApplicationCamperData;

    /**
     * Creates application camper data from an application.
     *
     * @param Application $application
     * @return \App\Library\Data\Common\ApplicationCamperData
     */
    public function createApplicationCamperDataFromApplication(Application $application): ApplicationCamperData;
}