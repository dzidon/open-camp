<?php

namespace App\Model\Service\Application;

use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;

/**
 * Creates applications.
 */
interface ApplicationFactoryInterface
{
    /**
     * Creates a new application and its attached entities.
     *
     * @param ApplicationStepOneData $data
     * @param CampDate $campDate
     * @param User|null $user
     * @return Application
     */
    public function createApplication(ApplicationStepOneData $data, CampDate $campDate, ?User $user = null): Application;
}