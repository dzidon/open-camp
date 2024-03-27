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
     * @return ApplicationCamperData
     */
    public function createApplicationCamperDataFromCampDate(CampDate $campDate): ApplicationCamperData;

    /**
     * Returns a callable that instantiates application camper data from a camp date.
     *
     * @param CampDate $campDate
     * @return callable
     */
    public function getApplicationCamperDataCallableFromCampDate(CampDate $campDate): callable;

    /**
     * Creates application camper data from an application.
     *
     * @param Application $application
     * @return ApplicationCamperData
     */
    public function createApplicationCamperDataFromApplication(Application $application): ApplicationCamperData;

    /**
     * Returns a callable that instantiates application camper data from an application.
     *
     * @param Application $application
     * @return callable
     */
    public function getApplicationCamperDataCallableFromApplication(Application $application): callable;
}