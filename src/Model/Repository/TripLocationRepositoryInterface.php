<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\TripLocationSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use Symfony\Component\Uid\UuidV4;

/**
 * Trip location CRUD.
 */
interface TripLocationRepositoryInterface
{
    /**
     * Saves a trip location.
     *
     * @param TripLocation $tripLocation
     * @param bool $flush
     * @return void
     */
    public function saveTripLocation(TripLocation $tripLocation, bool $flush): void;

    /**
     * Removes a trip location.
     *
     * @param TripLocation $tripLocation
     * @param bool $flush
     * @return void
     */
    public function removeTripLocation(TripLocation $tripLocation, bool $flush): void;

    /**
     * Creates a trip location.
     *
     * @param string $name
     * @param float $price
     * @param int $priority
     * @param TripLocationPath $tripLocationPath
     * @return TripLocation
     */
    public function createTripLocation(string           $name,
                                       float            $price,
                                       int              $priority,
                                       TripLocationPath $tripLocationPath): TripLocation;

    /**
     * Finds one trip location by id.
     *
     * @param UuidV4 $id
     * @return TripLocation|null
     */
    public function findOneById(UuidV4 $id): ?TripLocation;

    /**
     * Finds one trip location by name.
     *
     * @param string $name
     * @return TripLocation[]
     */
    public function findByName(string $name): array;

    /**
     * Finds trip locations by trip location path.
     *
     * @param TripLocationPath $tripLocationPath
     * @return TripLocation[]
     */
    public function findByTripLocationPath(TripLocationPath $tripLocationPath): array;

    /**
     * Returns true if the given trip location can be removed.
     *
     * @param TripLocation $tripLocation
     * @return bool
     */
    public function canRemoveTripLocation(TripLocation $tripLocation): bool;

    /**
     * Returns admin trip location search paginator.
     *
     * @param TripLocationSearchData $data
     * @param TripLocationPath $tripLocationPath
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(TripLocationSearchData $data,
                                      TripLocationPath       $tripLocationPath,
                                      int                    $currentPage,
                                      int                    $pageSize): PaginatorInterface;
}