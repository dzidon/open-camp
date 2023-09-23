<?php

namespace App\Model\Repository;

use App\Library\Data\User\CamperSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use Symfony\Component\Uid\UuidV4;

interface CamperRepositoryInterface
{
    /**
     * Saves a camper.
     *
     * @param Camper $camper
     * @param bool $flush
     * @return void
     */
    public function saveCamper(Camper $camper, bool $flush): void;

    /**
     * Removes a camper.
     *
     * @param Camper $camper
     * @param bool $flush
     * @return void
     */
    public function removeCamper(Camper $camper, bool $flush): void;

    /**
     * Finds one camper by id.
     *
     * @param UuidV4 $id
     * @return Camper|null
     */
    public function findOneById(UuidV4 $id): ?Camper;

    /**
     * Finds campers assigned to the given user.
     *
     * @param User $user
     * @return Camper[]
     */
    public function findByUser(User $user): array;

    /**
     * Finds other campers that have the same user as the given camper.
     *
     * @param Camper $camper
     * @return Camper[]
     */
    public function findOwnedBySameUser(Camper $camper): array;

    /**
     * Returns user camper search paginator.
     *
     * @param CamperSearchData $data
     * @param User $user
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getUserPaginator(CamperSearchData $data, User $user, int $currentPage, int $pageSize): PaginatorInterface;
}