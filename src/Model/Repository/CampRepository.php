<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\CampSearchDataInterface;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Camp;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method Camp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Camp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Camp[]    findAll()
 * @method Camp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampRepository extends AbstractRepository implements CampRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Camp::class);
    }

    /**
     * @inheritDoc
     */
    public function saveCamp(Camp $camp, bool $flush): void
    {
        $this->save($camp, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCamp(Camp $camp, bool $flush): void
    {
        $this->remove($camp, $flush);
    }

    /**
     * @inheritDoc
     */
    public function createCamp(string $name, string $urlName, int $ageMin, int $ageMax): Camp
    {
        return new Camp($name, $urlName, $ageMin, $ageMax);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?Camp
    {
        return $this->createQueryBuilder('camp')
            ->select('camp, campCategory')
            ->leftJoin('camp.campCategory', 'campCategory')
            ->andWhere('camp.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByUrlName(string $urlName): ?Camp
    {
        return $this->createQueryBuilder('camp')
            ->select('camp, campCategory')
            ->leftJoin('camp.campCategory', 'campCategory')
            ->andWhere('camp.urlName = :urlName')
            ->setParameter('urlName', $urlName)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(CampSearchDataInterface $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $age = $data->getAge();
        $campCategory = $data->getCampCategory();

        $queryBuilder = $this->createQueryBuilder('camp')
            ->andWhere('(camp.name LIKE :phrase OR camp.urlName LIKE :phrase)')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($age !== null)
        {
            $queryBuilder
                ->andWhere(':age >= camp.ageMin')
                ->andWhere(':age <= camp.ageMax')
                ->setParameter('age', $age)
            ;
        }

        if ($campCategory !== null)
        {
            $queryBuilder
                ->andWhere('camp.campCategory = :campCategoryId')
                ->setParameter('campCategoryId', $campCategory->getId(), UuidType::NAME)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}