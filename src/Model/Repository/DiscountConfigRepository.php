<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\DiscountConfigSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\DiscountConfig;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method DiscountConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiscountConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiscountConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscountConfigRepository extends AbstractRepository implements DiscountConfigRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscountConfig::class);
    }

    /**
     * @inheritDoc
     */
    public function saveDiscountConfig(DiscountConfig $discountConfig, bool $flush): void
    {
        $this->save($discountConfig, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeDiscountConfig(DiscountConfig $discountConfig, bool $flush): void
    {
        $this->remove($discountConfig, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('discountConfig')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?DiscountConfig
    {
        return $this->createQueryBuilder('discountConfig')
            ->andWhere('discountConfig.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?DiscountConfig
    {
        return $this->createQueryBuilder('discountConfig')
            ->andWhere('discountConfig.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(DiscountConfigSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $queryBuilder = $this->createQueryBuilder('discountConfig')
            ->andWhere('discountConfig.name LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}