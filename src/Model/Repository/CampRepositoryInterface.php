<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\CampSearchDataInterface;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use Symfony\Component\Uid\UuidV4;

/**
 * Camp CRUD.
 */
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
     * Creates a camp.
     *
     * @param string $name
     * @param string $urlName
     * @param int $ageMin
     * @param int $ageMax
     * @return Camp
     */
    public function createCamp(string $name, string $urlName, int $ageMin, int $ageMax): Camp;

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
     * @param CampSearchDataInterface $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(CampSearchDataInterface $data, int $currentPage, int $pageSize): PaginatorInterface;
}