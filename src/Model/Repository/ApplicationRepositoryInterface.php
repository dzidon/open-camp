<?php

namespace App\Model\Repository;

use App\Model\Entity\Application;
use App\Model\Library\Application\ApplicationsEditableDraftsResultInterface;
use Symfony\Component\Uid\UuidV4;

interface ApplicationRepositoryInterface
{
    /**
     * Saves an application.
     *
     * @param Application $application
     * @param bool $flush
     * @return void
     */
    public function saveApplication(Application $application, bool $flush): void;

    /**
     * Removes an application.
     *
     * @param Application $application
     * @param bool $flush
     * @return void
     */
    public function removeApplication(Application $application, bool $flush): void;

    /**
     * Finds one application by id.
     *
     * @param UuidV4 $id
     * @return Application|null
     */
    public function findOneById(UuidV4 $id): ?Application;

    /**
     * Finds one application by simple id.
     *
     * @param string $simpleId
     * @return Application|null
     */
    public function findOneBySimpleId(string $simpleId): ?Application;

    /**
     * Returns true if there is an application with the given simple id.
     *
     * @param string $simpleId
     * @return bool
     */
    public function simpleIdExists(string $simpleId): bool;

    /**
     * Returns information about what applications are editable drafts.
     *
     * @param Application[]|UuidV4[] $applications
     * @return ApplicationsEditableDraftsResultInterface
     */
    public function getApplicationsEditableDraftsResult(array $applications): ApplicationsEditableDraftsResultInterface;
}