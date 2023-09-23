<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\CampDateSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use DateTimeInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;
use Symfony\Component\Uid\UuidV4;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @method CampDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampDateRepository extends AbstractRepository implements CampDateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampDate::class);
    }

    /**
     * @inheritDoc
     */
    public function saveCampDate(CampDate $campDate, bool $flush): void
    {
        $this->save($campDate, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCampDate(CampDate $campDate, bool $flush): void
    {
        $this->remove($campDate, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?CampDate
    {
        return $this->createQueryBuilder('campDate')
            ->select('campDate, camp, leader')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('campDate.leaders', 'leader')
            ->andWhere('campDate.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findUpcomingByCamp(Camp $camp): array
    {
        return $this->createQueryBuilder('campDate')
            ->select('campDate, camp, leader')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('campDate.leaders', 'leader')
            ->andWhere('campDate.camp = :campId')
            ->setParameter('campId', $camp->getId(), UuidType::NAME)
            ->andWhere('campDate.startAt > :now')
            ->setParameter('now', new DateTimeImmutable('now'))
            ->orderBy('campDate.startAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findThoseThatCollideWithInterval(?Camp $camp, DateTimeInterface $startAt, DateTimeInterface $endAt): array
    {
        $queryBuilder = $this->createQueryBuilder('campDate')
            ->select('campDate, camp, leader')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('campDate.leaders', 'leader')
            ->andWhere('
                (:startAt >= campDate.startAt AND :startAt <= campDate.endAt) OR
                (:endAt   >= campDate.startAt AND :endAt <= campDate.endAt)   OR
                (campDate.startAt >= :startAt AND campDate.endAt <= :endAt)
            ')
            ->setParameter('startAt', $startAt)
            ->setParameter('endAt', $endAt)
        ;

        if ($camp !== null)
        {
            $queryBuilder
                ->andWhere('camp.id = :id')
                ->setParameter('id', $camp->getId(), UuidType::NAME)
            ;
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(CampDateSearchData $data, Camp $camp, int $currentPage, int $pageSize): PaginatorInterface
    {
        $from = $data->getFrom();
        $to = $data->getTo();
        $sortBy = $data->getSortBy();
        $isHistorical = $data->isHistorical();
        $isActive = $data->isActive();

        $queryBuilder = $this->createQueryBuilder('campDate')
            ->andWhere('campDate.camp = :id')
            ->setParameter('id', $camp->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($from !== null)
        {
            $queryBuilder
                ->andWhere('campDate.startAt >= :from')
                ->setParameter('from', $from->format('Y-m-d 00:00:00'))
            ;
        }

        if ($to !== null)
        {
            $queryBuilder
                ->andWhere('campDate.endAt <= :to')
                ->setParameter('to', $to->format('Y-m-d 23:59:59'))
            ;
        }

        if ($isHistorical === true)
        {
            $queryBuilder
                ->andWhere('campDate.endAt < :now')
                ->setParameter('now', new DateTimeImmutable('now'))
            ;
        }
        else if ($isHistorical === false)
        {
            $queryBuilder
                ->andWhere('campDate.endAt >= :now')
                ->setParameter('now', new DateTimeImmutable('now'))
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}