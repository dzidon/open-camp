<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PurchasableItemVariantValueSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method PurchasableItemVariantValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchasableItemVariantValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchasableItemVariantValue[]    findAll()
 * @method PurchasableItemVariantValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasableItemVariantValueRepository extends AbstractRepository implements PurchasableItemVariantValueRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchasableItemVariantValue::class);
    }

    /**
     * @inheritDoc
     */
    public function savePurchasableItemVariantValue(PurchasableItemVariantValue $purchasableItemVariantValue, bool $flush): void
    {
        $this->save($purchasableItemVariantValue, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removePurchasableItemVariantValue(PurchasableItemVariantValue $purchasableItemVariantValue, bool $flush): void
    {
        $this->remove($purchasableItemVariantValue, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?PurchasableItemVariantValue
    {
        return $this->createQueryBuilder('purchasableItemVariantValue')
            ->select('purchasableItemVariantValue, purchasableItemVariant, purchasableItem')
            ->leftJoin('purchasableItemVariantValue.purchasableItemVariant', 'purchasableItemVariant')
            ->leftJoin('purchasableItemVariant.purchasableItem', 'purchasableItem')
            ->andWhere('purchasableItemVariantValue.id = :id')
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
        return $this->createQueryBuilder('purchasableItemVariantValue')
            ->select('purchasableItemVariantValue, purchasableItemVariant, purchasableItem')
            ->leftJoin('purchasableItemVariantValue.purchasableItemVariant', 'purchasableItemVariant')
            ->leftJoin('purchasableItemVariant.purchasableItem', 'purchasableItem')
            ->andWhere('purchasableItemVariantValue.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function canRemovePurchasableItemVariantValue(PurchasableItemVariantValue $purchasableItemVariantValue): bool
    {
        $purchasableItemVariant = $purchasableItemVariantValue->getPurchasableItemVariant();

        $count = $this->createQueryBuilder('purchasableItemVariantValue')
            ->select('count(purchasableItemVariantValue.id)')
            ->andWhere('purchasableItemVariantValue.purchasableItemVariant = :purchasableItemVariantId')
            ->setParameter('purchasableItemVariantId', $purchasableItemVariant->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 1;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(PurchasableItemVariantValueSearchData $data, PurchasableItemVariant $purchasableItemVariant, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('purchasableItemVariantValue')
            ->select('purchasableItemVariantValue, purchasableItemVariant, purchasableItem')
            ->leftJoin('purchasableItemVariantValue.purchasableItemVariant', 'purchasableItemVariant')
            ->leftJoin('purchasableItemVariant.purchasableItem', 'purchasableItem')
            ->andWhere('purchasableItemVariantValue.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->andWhere('purchasableItemVariantValue.purchasableItemVariant = :id')
            ->setParameter('id', $purchasableItemVariant->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}