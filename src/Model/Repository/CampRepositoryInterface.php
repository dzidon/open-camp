<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\CampSearchData as AdminCampSearchData;
use App\Library\Data\User\CampSearchData as UserCampSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampCategory;
use App\Model\Library\Camp\CampLifespanCollectionInterface;
use App\Model\Library\Camp\UserCampCatalogResultInterface;
use Symfony\Component\Uid\UuidV4;

interface CampRepositoryInterface
{
    /**
     * Saves a camp.
     *
     * @param Camp $camp
     * @param bool $flush
     * @return void
     */
    public function saveCamp(Camp $camp, bool $flush): void;

    /**
     * Removes a camp.
     *
     * @param Camp $camp
     * @param bool $flush
     * @return void
     */
    public function removeCamp(Camp $camp, bool $flush): void;

    /**
     * Finds one camp by id.
     *
     * @param UuidV4 $id
     * @return Camp|null
     */
    public function findOneById(UuidV4 $id): ?Camp;

    /**
     * Returns a camp with the given url name.
     *
     * @param string $urlName
     * @return Camp|null
     */
    public function findOneByUrlName(string $urlName): ?Camp;

    /**
     * Returns admin camp search paginator.
     *
     * @param AdminCampSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(AdminCampSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;

    /**
     * Returns user camp catalog search result.
     *
     * @param UserCampSearchData $data
     * @param CampCategory|null $campCategory
     * @param bool $showHidden
     * @param int $currentPage
     * @param int $pageSize
     * @return UserCampCatalogResultInterface
     */
    public function getUserCampCatalogResult(UserCampSearchData $data,
                                             ?CampCategory      $campCategory,
                                             bool               $showHidden,
                                             int                $currentPage,
                                             int                $pageSize): UserCampCatalogResultInterface;

    /**
     * Takes an array of camps and fetches a collection of their min start dates and max end dates.
     *
     * @param Camp[] $camps
     * @return CampLifespanCollectionInterface
     */
    public function getCampLifespanCollection(array $camps): CampLifespanCollectionInterface;
}