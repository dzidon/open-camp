<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\CampSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use App\Model\Module\CampCatalog\Camp\CampLifespanCollectionInterface;
use App\Model\Module\CampCatalog\Camp\CampLifespanInterface;
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
     * @param string $street
     * @param string $town
     * @param string $zip
     * @param string $country
     * @return Camp
     */
    public function createCamp(string $name,
                               string $urlName,
                               int    $ageMin,
                               int    $ageMax,
                               string $street,
                               string $town,
                               string $zip,
                               string $country): Camp;

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
     * @param CampSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(CampSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;

    /**
     * Takes an array of camps and fetches a collection of their min start dates and max end dates.
     *
     * @param Camp[] $camps
     * @return CampLifespanCollectionInterface
     */
    public function getCampLifespanCollection(array $camps): CampLifespanCollectionInterface;
}