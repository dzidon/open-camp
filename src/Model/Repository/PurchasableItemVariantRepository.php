<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PurchasableItemVariantSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method PurchasableItemVariant|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchasableItemVariant|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchasableItemVariant[]    findAll()
 * @method PurchasableItemVariant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasableItemVariantRepository extends AbstractRepository implements PurchasableItemVariantRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchasableItemVariant::class);
    }

    /**
     * @inheritDoc
     */
    public function savePurchasableItemVariant(PurchasableItemVariant $purchasableItemVariant, bool $flush): void
    {
        $this->save($purchasableItemVariant, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removePurchasableItemVariant(PurchasableItemVariant $purchasableItemVariant, bool $flush): void
    {
        $this->remove($purchasableItemVariant, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?PurchasableItemVariant
    {
        return $this->createQueryBuilder('purchasableItemVariant')
            ->select('purchasableItemVariant, purchasableItem, purchasableItemVariantValue')
            ->leftJoin('purchasableItemVariant.purchasableItem', 'purchasableItem')
            ->leftJoin('purchasableItemVariant.purchasableItemVariantValues', 'purchasableItemVariantValue')
            ->andWhere('purchasableItemVariant.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->orderBy('purchasableItemVariantValue.priority', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('purchasableItemVariant')
            ->select('purchasableItemVariant, purchasableItem, purchasableItemVariantValue')
            ->leftJoin('purchasableItemVariant.purchasableItem', 'purchasableItem')
            ->leftJoin('purchasableItemVariant.purchasableItemVariantValues', 'purchasableItemVariantValue')
            ->andWhere('purchasableItemVariant.name = :name')
            ->setParameter('name', $name)
            ->orderBy('purchasableItemVariantValue.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(PurchasableItemVariantSearchData $data, PurchasableItem $purchasableItem, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('purchasableItemVariant')
            ->select('purchasableItemVariant, purchasableItem')
            ->leftJoin('purchasableItemVariant.purchasableItem', 'purchasableItem')
            ->andWhere('purchasableItemVariant.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->andWhere('purchasableItemVariant.purchasableItem = :id')
            ->setParameter('id', $purchasableItem->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}