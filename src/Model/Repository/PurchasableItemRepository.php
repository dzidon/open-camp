<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PurchasableItemSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\PurchasableItem;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method PurchasableItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchasableItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchasableItem[]    findAll()
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
    public function findOneById(UuidV4 $id): ?PurchasableItem
    {
        return $this->createQueryBuilder('purchasableItem')
            ->andWhere('purchasableItem.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?PurchasableItem
    {
        return $this->createQueryBuilder('purchasableItem')
            ->andWhere('purchasableItem.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(PurchasableItemSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('purchasableItem')
            ->andWhere('purchasableItem.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}