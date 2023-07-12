<?php

namespace App\Model\Repository;

use App\Enum\GenderEnum;
use App\Form\DataTransfer\Data\User\CamperSearchDataInterface;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Search\Paginator\PaginatorInterface;
use DateTimeImmutable;

/**
 * Camper CRUD.
 */
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
     * Creates a camper.
     *
     * @param string $name
     * @param GenderEnum $gender
     * @param DateTimeImmutable $bornAt
     * @param User $user
     * @return Camper
     */
    public function createCamper(string            $name,
                                 GenderEnum        $gender,
                                 DateTimeImmutable $bornAt,
                                 User              $user): Camper;

    /**
     * Finds one camper by id.
     *
     * @param int $id
     * @return Camper|null
     */
    public function findOneById(int $id): ?Camper;

    /**
     * Returns user camper search paginator.
     *
     * @param CamperSearchDataInterface $data
     * @param User $user
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getUserPaginator(CamperSearchDataInterface $data, User $user, int $currentPage, int $pageSize): PaginatorInterface;
}