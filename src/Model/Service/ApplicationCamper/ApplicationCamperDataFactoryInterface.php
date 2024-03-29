<?php

namespace App\Model\Service\ApplicationCamper;

use App\Library\Data\Common\ApplicationCamperData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
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
     * @param bool $isMedicalDiaryEnabled
     * @return ApplicationCamperData
     */
    public function createApplicationCamperDataFromCampDate(CampDate $campDate, bool $isMedicalDiaryEnabled): ApplicationCamperData;

    /**
     * Returns a callable that instantiates application camper data from a camp date.
     *
     * @param CampDate $campDate
     * @param bool $isMedicalDiaryEnabled
     * @return callable
     */
    public function getApplicationCamperDataCallableFromCampDate(CampDate $campDate, bool $isMedicalDiaryEnabled): callable;

    /**
     * Creates application camper data from an application.
     *
     * @param Application $application
     * @param bool $isMedicalDiaryEnabled
     * @return ApplicationCamperData
     */
    public function createApplicationCamperDataFromApplication(Application $application, bool $isMedicalDiaryEnabled): ApplicationCamperData;

    /**
     * Returns a callable that instantiates application camper data from an application.
     *
     * @param Application $application
     * @param bool $isMedicalDiaryEnabled
     * @return callable
     */
    public function getApplicationCamperDataCallableFromApplication(Application $application, bool $isMedicalDiaryEnabled): callable;

    /**
     * Creates application camper data from an application camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @param bool $isMedicalDiaryEnabled
     * @return ApplicationCamperData
     */
    public function createApplicationCamperDataFromApplicationCamper(ApplicationCamper $applicationCamper, bool $isMedicalDiaryEnabled): ApplicationCamperData;

    /**
     * Returns a callable that instantiates application camper data from an application camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @param bool $isMedicalDiaryEnabled
     * @return callable
     */
    public function getApplicationCamperDataCallableFromApplicationCamper(ApplicationCamper $applicationCamper, bool $isMedicalDiaryEnabled): callable;
}