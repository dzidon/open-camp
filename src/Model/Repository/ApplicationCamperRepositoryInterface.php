<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationCamperSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\CampDate;
use Symfony\Component\Uid\UuidV4;

interface ApplicationCamperRepositoryInterface
{
    /**
     * Saves an application camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @param bool $flush
     * @return void
     */
    public function saveApplicationCamper(ApplicationCamper $applicationCamper, bool $flush): void;

    /**
     * Removes an application camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @param bool $flush
     * @return void
     */
    public function removeApplicationCamper(ApplicationCamper $applicationCamper, bool $flush): void;

    /**
     * Finds one application camper by id.
     *
     * @param UuidV4 $id
     * @return ApplicationCamper|null
     */
    public function findOneById(UuidV4 $id): ?ApplicationCamper;

    /**
     * Finds application campers who occupy slots in the given camp date.
     *
     * @param CampDate $campDate
     * @return ApplicationCamper[]
     */
    public function findThoseThatOccupySlotsInCampDate(CampDate $campDate): array;

    /**
     * Finds accepted application campers assigned the given camp date.
     *
     * @param CampDate $campDate
     * @return ApplicationCamper[]
     */
    public function findAcceptedByCampDate(CampDate $campDate): array;

    /**
     * Returns the number of other complete (isDraft = false) and accepted applications that contain the given camper.
     *
     * @param ApplicationCamper $applicationCamper
     * @return int
     */
    public function getNumberOfOtherCompleteAcceptedApplications(ApplicationCamper $applicationCamper): int;

    /**
     * @param CampDate $campDate
     * @return int
     */
    public function getNumberOfAcceptedApplicationCampersForCampDate(CampDate $campDate): int;

    /**
     * Returns admin application camper search paginator.
     *
     * @param ApplicationCamperSearchData $data
     * @param Application|CampDate $applicationOrCampDate
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(ApplicationCamperSearchData $data,
                                      Application|CampDate        $applicationOrCampDate,
                                      int                         $currentPage,
                                      int                         $pageSize): PaginatorInterface;
}