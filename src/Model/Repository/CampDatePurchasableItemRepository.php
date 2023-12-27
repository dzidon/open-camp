<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDatePurchasableItem;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @method CampDatePurchasableItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampDatePurchasableItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampDatePurchasableItem[]    findAll()
 * @method CampDatePurchasableItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampDatePurchasableItemRepository extends AbstractRepository implements CampDatePurchasableItemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampDatePurchasableItem::class);
    }

    /**
     * @inheritDoc
     */
    public function saveCampDatePurchasableItem(CampDatePurchasableItem $campDatePurchasableItem, bool $flush): void
    {
        $this->save($campDatePurchasableItem, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCampDatePurchasableItem(CampDatePurchasableItem $campDatePurchasableItem, bool $flush): void
    {
        $this->remove($campDatePurchasableItem, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findByCampDate(CampDate $campDate): array
    {
        return $this->createQueryBuilder('campDatePurchasableItem')
            ->select('campDatePurchasableItem, campDate, purchasableItem')
            ->leftJoin('campDatePurchasableItem.campDate', 'campDate')
            ->leftJoin('campDatePurchasableItem.purchasableItem', 'purchasableItem')
            ->andWhere('campDatePurchasableItem.campDate = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->orderBy('campDatePurchasableItem.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}