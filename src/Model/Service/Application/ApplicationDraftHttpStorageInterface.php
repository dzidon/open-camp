<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\UuidV4;

/**
 * Stores ids of user's application drafts.
 */
interface ApplicationDraftHttpStorageInterface
{
    /**
     * Stores the application draft using the given response.
     *
     * @param Application $application
     * @param Response $response
     * @return void
     */
    public function storeApplicationDraft(Application $application, Response $response): void;

    /**
     * Retrieves the ID of the application draft associated with the given camp date.
     *
     * @param CampDate $campDate
     * @return UuidV4|null
     */
    public function getApplicationDraftId(CampDate $campDate): ?UuidV4;

    /**
     * Retrieves an array of UUIDs representing all user's application draft IDs.
     *
     * @return UuidV4[]
     */
    public function getApplicationDraftIds(): array;

    /**
     * Removes the application draft id associated with the given target (either CampDate or Application).
     *
     * @param CampDate|Application $target
     * @param Response $response
     * @return void
     */
    public function removeApplicationDraft(CampDate|Application $target, Response $response): void;
}