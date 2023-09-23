<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\TripLocationSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method TripLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TripLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TripLocation[]    findAll()
 * @method TripLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripLocationRepository extends AbstractRepository implements TripLocationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripLocation::class);
    }

    /**
     * @inheritDoc
     */
    public function saveTripLocation(TripLocation $tripLocation, bool $flush): void
    {
        $this->save($tripLocation, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeTripLocation(TripLocation $tripLocation, bool $flush): void
    {
        $this->remove($tripLocation, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?TripLocation
    {
        return $this->createQueryBuilder('tripLocation')
            ->select('tripLocation, tripLocationPath')
            ->leftJoin('tripLocation.tripLocationPath', 'tripLocationPath')
            ->andWhere('tripLocation.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('tripLocation')
            ->select('tripLocation, tripLocationPath')
            ->leftJoin('tripLocation.tripLocationPath', 'tripLocationPath')
            ->andWhere('tripLocation.name = :name')
            ->setParameter('name', $name)
            ->orderBy('tripLocation.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByTripLocationPath(TripLocationPath $tripLocationPath): array
    {
        return $this->createQueryBuilder('tripLocation')
            ->select('tripLocation, tripLocationPath')
            ->leftJoin('tripLocation.tripLocationPath', 'tripLocationPath')
            ->andWhere('tripLocation.tripLocationPath = :id')
            ->setParameter('id', $tripLocationPath->getId(), UuidType::NAME)
            ->orderBy('tripLocation.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function canRemoveTripLocation(TripLocation $tripLocation): bool
    {
        $tripLocationPath = $tripLocation->getTripLocationPath();

        $count = $this->createQueryBuilder('tripLocation')
            ->select('count(tripLocation.id)')
            ->andWhere('tripLocation.tripLocationPath = :tripLocationPathId')
            ->setParameter('tripLocationPathId', $tripLocationPath->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 1;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(TripLocationSearchData $data,
                                      TripLocationPath       $tripLocationPath,
                                      int                    $currentPage,
                                      int                    $pageSize): PaginatorInterface
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('tripLocation')
            ->select('tripLocation, tripLocationPath')
            ->leftJoin('tripLocation.tripLocationPath', 'tripLocationPath')
            ->andWhere('tripLocation.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->andWhere('tripLocation.tripLocationPath = :id')
            ->setParameter('id', $tripLocationPath->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}