<?php

namespace App\Service\Data\Factory\Application;

use App\Library\Data\Common\ContactData;
use App\Library\Data\User\ApplicationCamperData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;

/**
 * Creates application step one data.
 */
interface ApplicationStepOneDataFactoryInterface
{
    /**
     * Creates data for the first step of application process.
     *
     * @param CampDate $campDate
     * @param User|null $authenticatedUser
     * @param ApplicationCamperData|null $defaultApplicationCamperData
     * @param ContactData|null $defaultContactData
     * @return ApplicationStepOneData
     */
    public function createApplicationStepOneData(CampDate               $campDate,
                                                 ?User                  $authenticatedUser = null,
                                                 ?ApplicationCamperData $defaultApplicationCamperData = null,
                                                 ?ContactData           $defaultContactData = null): ApplicationStepOneData;
}