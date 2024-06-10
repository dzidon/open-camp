<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationSearchData;
use App\Library\Data\User\ApplicationProfileSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Application;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Library\Application\ApplicationsEditableDraftsResultInterface;
use App\Model\Library\Application\ApplicationTotalRevenueResultInterface;
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
     * Returns the last completed application from session.
     *
     * @return Application|null
     */
    public function findLastCompletedFromSession(): ?Application;

    /**
     * Finds one application by simple id.
     *
     * @param string $simpleId
     * @return Application|null
     */
    public function findOneBySimpleId(string $simpleId): ?Application;

    /**
     * Finds accepted applications assigned the given camp date.
     *
     * @param CampDate $campDate
     * @return Application[]
     */
    public function findAcceptedByCampDate(CampDate $campDate): array;

    /**
     * Returns true if there is an application with the given simple id.
     *
     * @param string $simpleId
     * @return bool
     */
    public function simpleIdExists(string $simpleId): bool;

    /**
     * Returns the highest invoice number.
     *
     * @return null|int
     */
    public function getHighestInvoiceNumber(): ?int;

    /**
     * Returns information about what applications are editable drafts.
     *
     * @param Application[]|UuidV4[] $applications
     * @return ApplicationsEditableDraftsResultInterface
     */
    public function getApplicationsEditableDraftsResult(array $applications): ApplicationsEditableDraftsResultInterface;

    /**
     * Returns user application search paginator.
     *
     * @param ApplicationProfileSearchData $data
     * @param User $user
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getUserPaginator(ApplicationProfileSearchData $data, User $user, int $currentPage, int $pageSize): PaginatorInterface;

    /**
     * Returns admin application search paginator.
     *
     * @param ApplicationSearchData $data
     * @param null|User|CampDate $guideOrCampDate
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(ApplicationSearchData $data,
                                      null|User|CampDate    $guideOrCampDate,
                                      int                   $currentPage,
                                      int                   $pageSize): PaginatorInterface;

    /**
     * Returns the total full prices in available currencies of accepted applications assigned to the given camp.
     *
     * @param Camp $camp
     * @return ApplicationTotalRevenueResultInterface
     */
    public function getTotalRevenueForCampResult(Camp $camp): ApplicationTotalRevenueResultInterface;

    /**
     * @param Camp $camp
     * @return int
     */
    public function getNumberOfAcceptedApplicationsForCamp(Camp $camp): int;

    /**
     * Returns the total full prices in available currencies of accepted applications assigned to the given camp date.
     *
     * @param CampDate $campDate
     * @return ApplicationTotalRevenueResultInterface
     */
    public function getTotalRevenueForCampDateResult(CampDate $campDate): ApplicationTotalRevenueResultInterface;

    /**
     * @param CampDate $campDate
     * @return int
     */
    public function getNumberOfAcceptedApplicationsForCampDate(CampDate $campDate): int;
}