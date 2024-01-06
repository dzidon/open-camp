<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PurchasableItemSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method PurchasableItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchasableItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchasableItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasableItemRepository extends AbstractRepository implements PurchasableItemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchasableItem::class);
    }

    /**
     * @inheritDoc
     */
    public function savePurchasableItem(PurchasableItem $purchasableItem, bool $flush): void
    {
        $this->save($purchasableItem, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removePurchasableItem(PurchasableItem $purchasableItem, bool $flush): void
    {
        $this->remove($purchasableItem, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        /** @var PurchasableItem[] $purchasableItems */
        $purchasableItems = $this->createQueryBuilder('purchasableItem')
            ->select('purchasableItem, purchasableItemVariant')
            ->leftJoin('purchasableItem.purchasableItemVariants', 'purchasableItemVariant')
            ->orderBy('purchasableItemVariant.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $purchasableItemVariants = $this->getPurchasableItemVariants($purchasableItems);
        $this->loadPurchasableItemVariantValues($purchasableItemVariants);

        return $purchasableItems;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?PurchasableItem
    {
        /** @var null|PurchasableItem $purchasableItem */
        $purchasableItem = $this->createQueryBuilder('purchasableItem')
            ->select('purchasableItem, purchasableItemVariant')
            ->leftJoin('purchasableItem.purchasableItemVariants', 'purchasableItemVariant')
            ->andWhere('purchasableItem.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->orderBy('purchasableItemVariant.priority', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $purchasableItemVariants = $this->getPurchasableItemVariants($purchasableItem);
        $this->loadPurchasableItemVariantValues($purchasableItemVariants);

        return $purchasableItem;
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?PurchasableItem
    {
        /** @var null|PurchasableItem $purchasableItem */
        $purchasableItem = $this->createQueryBuilder('purchasableItem')
            ->select('purchasableItem, purchasableItemVariant')
            ->leftJoin('purchasableItem.purchasableItemVariants', 'purchasableItemVariant')
            ->andWhere('purchasableItem.name = :name')
            ->setParameter('name', $name)
            ->orderBy('purchasableItemVariant.priority', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $purchasableItemVariants = $this->getPurchasableItemVariants($purchasableItem);
        $this->loadPurchasableItemVariantValues($purchasableItemVariants);

        return $purchasableItem;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(PurchasableItemSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $isGlobal = $data->isGlobal();

        $queryBuilder = $this->createQueryBuilder('purchasableItem')
            ->andWhere('purchasableItem.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($isGlobal !== null)
        {
            $queryBuilder
                ->andWhere('purchasableItem.isGlobal = :isGlobal')
                ->setParameter('isGlobal', $isGlobal)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }

    private function loadPurchasableItemVariantValues(null|array|PurchasableItemVariant $purchasableItemVariants): void
    {
        if (empty($purchasableItemVariants))
        {
            return;
        }

        $purchasableItemVariantIds = $this->getPurchasableItemVariantIds($purchasableItemVariants);

        $this->_em->createQueryBuilder()
            ->select('purchasableItemVariant, purchasableItemVariantValue')
            ->from(PurchasableItemVariant::class, 'purchasableItemVariant')
            ->leftJoin('purchasableItemVariant.purchasableItemVariantValues', 'purchasableItemVariantValue')
            ->andWhere('purchasableItemVariant.id IN (:ids)')
            ->setParameter('ids', $purchasableItemVariantIds)
            ->orderBy('purchasableItemVariantValue.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param null|array|PurchasableItem $purchasableItems
     * @return PurchasableItemVariant[]
     */
    private function getPurchasableItemVariants(null|array|PurchasableItem $purchasableItems): array
    {
        if ($purchasableItems === null)
        {
            return [];
        }

        if ($purchasableItems instanceof PurchasableItem)
        {
            $purchasableItems = [$purchasableItems];
        }

        $purchasableItemVariants = [];

        foreach ($purchasableItems as $purchasableItem)
        {
            foreach ($purchasableItem->getPurchasableItemVariants() as $purchasableItemVariant)
            {
                $purchasableItemVariants[] = $purchasableItemVariant;
            }
        }

        return $purchasableItemVariants;
    }

    private function getPurchasableItemVariantIds(array|PurchasableItemVariant $purchasableItemVariants): array
    {
        if ($purchasableItemVariants instanceof PurchasableItemVariant)
        {
            $purchasableItemVariants = [$purchasableItemVariants];
        }

        return array_map(function (PurchasableItemVariant $purchasableItemVariant) {
            return $purchasableItemVariant->getId()->toBinary();
        }, $purchasableItemVariants);
    }
}