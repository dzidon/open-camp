<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\CampDateSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Uid\UuidV4;

/**
 * Camp date CRUD.
 */
interface CampDateRepositoryInterface
{
    /**
     * Saves a camp date.
     *
     * @param CampDate $campDate
     * @param bool $flush
     * @return void
     */
    public function saveCampDate(CampDate $campDate, bool $flush): void;

    /**
     * Removes a camp date.
     *
     * @param CampDate $campDate
     * @param bool $flush
     * @return void
     */
    public function removeCampDate(CampDate $campDate, bool $flush): void;

    /**
     * Creates a camp date.
     *
     * @param DateTimeImmutable $startAt
     * @param DateTimeImmutable $endAt
     * @param float $price
     * @param int $capacity
     * @param Camp $camp
     * @return CampDate
     */
    public function createCampDate(DateTimeImmutable $startAt, DateTimeImmutable $endAt, float $price, int $capacity, Camp $camp): CampDate;

    /**
     * Finds one camp date by id.
     *
     * @param UuidV4 $id
     * @return CampDate|null
     */
    public function findOneById(UuidV4 $id): ?CampDate;

    /**
     * Finds dates of the given camp that collide with the given datetime interval.
     *
     * @param null|Camp $camp
     * @param DateTimeInterface $startAt
     * @param DateTimeInterface $endAt
     * @return CampDate[]
     */
    public function findThoseThatCollideWithInterval(?Camp $camp, DateTimeInterface $startAt, DateTimeInterface $endAt): array;

    /**
     * Returns admin camp search paginator.
     *
     * @param CampDateSearchData $data
     * @param Camp $camp
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(CampDateSearchData $data, Camp $camp, int $currentPage, int $pageSize): PaginatorInterface;
}