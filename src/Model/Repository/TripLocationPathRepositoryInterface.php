<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\TripLocationPathSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\TripLocationPath;
use Symfony\Component\Uid\UuidV4;

interface TripLocationPathRepositoryInterface
{
    /**
     * Saves a trip location path.
     *
     * @param TripLocationPath $tripLocationPath
     * @param bool $flush
     * @return void
     */
    public function saveTripLocationPath(TripLocationPath $tripLocationPath, bool $flush): void;

    /**
     * Removes a trip location path.
     *
     * @param TripLocationPath $tripLocationPath
     * @param bool $flush
     * @return void
     */
    public function removeTripLocationPath(TripLocationPath $tripLocationPath, bool $flush): void;

    /**
     * Finds one trip location path by id.
     *
     * @param UuidV4 $id
     * @return TripLocationPath|null
     */
    public function findOneById(UuidV4 $id): ?TripLocationPath;

    /**
     * Finds one trip location path by name.
     *
     * @param string $name
     * @return TripLocationPath|null
     */
    public function findOneByName(string $name): ?TripLocationPath;

    /**
     * Returns admin trip location path search paginator.
     *
     * @param TripLocationPathSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(TripLocationPathSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}