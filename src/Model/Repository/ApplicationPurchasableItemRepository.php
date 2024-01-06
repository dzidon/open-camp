<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPurchasableItem;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApplicationPurchasableItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationPurchasableItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationPurchasableItem[]    findAll()
 * @method ApplicationPurchasableItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationPurchasableItemRepository extends AbstractRepository implements ApplicationPurchasableItemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationPurchasableItem::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationPurchasableItem(ApplicationPurchasableItem $ApplicationPurchasableItem, bool $flush): void
    {
        $this->save($ApplicationPurchasableItem, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationPurchasableItem(ApplicationPurchasableItem $ApplicationPurchasableItem, bool $flush): void
    {
        $this->remove($ApplicationPurchasableItem, $flush);
    }
}