<?php

namespace App\Model\Service\Application;

use App\Library\Data\User\ApplicationCamperData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Library\Data\User\ContactData;
use App\Model\Entity\CampDate;

/**
 * Creates application step one data.
 */
interface ApplicationStepOneDataFactoryInterface
{
    /**
     * Creates data for the first step of application process.
     *
     * @param CampDate $campDate
     * @param ApplicationCamperData|null $defaultApplicationCamperData
     * @param ContactData|null $defaultContactData
     * @return ApplicationStepOneData
     */
    public function createApplicationStepOneData(CampDate               $campDate,
                                                 ?ApplicationCamperData $defaultApplicationCamperData = null,
                                                 ?ContactData           $defaultContactData = null): ApplicationStepOneData;
}