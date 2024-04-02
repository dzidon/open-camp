<?php

namespace App\Service\Data\Factory\ApplicationCamper;

use App\Library\Data\User\ApplicationCamperData as UserApplicationCamperData;
use App\Library\Data\Admin\ApplicationCamperData as AdminApplicationCamperData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\CampDate;

/**
 * Creates application camper data.
 */
interface ApplicationCamperDataFactoryInterface
{
    /**
     * @param CampDate $campDate
     * @return UserApplicationCamperData
     */
    public function createFromCampDateForUserModule(CampDate $campDate): UserApplicationCamperData;

    /**
     * @param CampDate $campDate
     * @return callable
     */
    public function getCallableFromCampDateForUserModule(CampDate $campDate): callable;

    /**
     * @param Application $application
     * @return UserApplicationCamperData
     */
    public function createFromApplicationForUserModule(Application $application): UserApplicationCamperData;

    /**
     * @param Application $application
     * @return callable
     */
    public function getCallableFromApplicationForUserModule(Application $application): callable;

    /**
     * @param ApplicationCamper $applicationCamper
     * @return UserApplicationCamperData
     */
    public function createFromApplicationCamperForUserModule(ApplicationCamper $applicationCamper): UserApplicationCamperData;

    /**
     * @param ApplicationCamper $applicationCamper
     * @return callable
     */
    public function getCallableFromApplicationCamperForUserModule(ApplicationCamper $applicationCamper): callable;

    /**
     * @param Application $application
     * @return AdminApplicationCamperData
     */
    public function createFromApplicationForAdminModule(Application $application): AdminApplicationCamperData;

    /**
     * @param Application $application
     * @return callable
     */
    public function getCallableFromApplicationForAdminModule(Application $application): callable;

    /**
     * @param ApplicationCamper $applicationCamper
     * @return AdminApplicationCamperData
     */
    public function createFromApplicationCamperForAdminModule(ApplicationCamper $applicationCamper): AdminApplicationCamperData;

    /**
     * @param ApplicationCamper $applicationCamper
     * @return callable
     */
    public function getCallableFromApplicationCamperForAdminModule(ApplicationCamper $applicationCamper): callable;
}