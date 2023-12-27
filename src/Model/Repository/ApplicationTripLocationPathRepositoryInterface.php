<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationTripLocationPath;

interface ApplicationTripLocationPathRepositoryInterface
{
    /**
     * Saves an application trip location path.
     *
     * @param ApplicationTripLocationPath $applicationTripLocationPath
     * @param bool $flush
     * @return void
     */
    public function saveApplicationTripLocationPath(ApplicationTripLocationPath $applicationTripLocationPath, bool $flush): void;

    /**
     * Removes an application trip location path.
     *
     * @param ApplicationTripLocationPath $applicationTripLocationPath
     * @param bool $flush
     * @return void
     */
    public function removeApplicationTripLocationPath(ApplicationTripLocationPath $applicationTripLocationPath, bool $flush): void;
}