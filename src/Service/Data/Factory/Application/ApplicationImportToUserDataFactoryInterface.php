<?php

namespace App\Service\Data\Factory\Application;

use App\Library\Data\User\ApplicationImportToUserData;
use App\Model\Entity\Application;
use App\Model\Entity\User;

/**
 * Creates a DTO that can be used import data from an application to a user.
 */
interface ApplicationImportToUserDataFactoryInterface
{
    /**
     * Creates a DTO that can be used import data from an application to a user.
     *
     * @param Application $application
     * @param User $user
     * @return ApplicationImportToUserData
     */
    public function createApplicationImportToUserData(Application $application, User $user): ApplicationImportToUserData;
}