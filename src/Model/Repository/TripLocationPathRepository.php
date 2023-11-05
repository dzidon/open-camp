<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\TripLocationPathSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\TripLocationPath;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method TripLocationPath|null find($id, $lockMode = null, $lockVersion = null)
 * @method TripLocationPath|null findOneBy(array $criteria, array $orderBy = null)
 * @method TripLocationPath[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripLocationPathRepository extends AbstractRepository implements TripLocationPathRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TripLocationPath::class);
    }

    /**
     * @inheritDoc
     */
    public function saveTripLocationPath(TripLocationPath $tripLocationPath, bool $flush): void
    {
        $this->save($tripLocationPath, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeTripLocationPath(TripLocationPath $tripLocationPath, bool $flush): void
    {
        $this->remove($tripLocationPath, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('tripLocationPath')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?TripLocationPath
    {
        return $this->createQueryBuilder('tripLocationPath')
            ->andWhere('tripLocationPath.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?TripLocationPath
    {
        return $this->createQueryBuilder('tripLocationPath')
            ->andWhere('tripLocationPath.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(TripLocationPathSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('tripLocationPath')
            ->andWhere('tripLocationPath.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}