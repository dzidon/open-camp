<?php

namespace App\Model\Service\Application;

use App\Library\Data\User\ApplicationImportToUserData;
use App\Model\Entity\Application;
use App\Model\Entity\User;

/**
 * Imports data from an application to a user.
 */
interface ApplicationToUserImporterInterface
{
    /**
     * Returns true if there is something to import from an application to a user.
     *
     * @param Application $application
     * @param User $user
     * @return bool
     */
    public function canImportApplicationToUser(Application $application, User $user): bool;

    /**
     * Imports data from an application to a user.
     *
     * @param ApplicationImportToUserData $data
     * @return void
     */
    public function importApplicationDataToUser(ApplicationImportToUserData $data): void;
}