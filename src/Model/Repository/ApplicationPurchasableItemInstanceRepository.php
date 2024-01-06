<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPurchasableItemInstance;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApplicationPurchasableItemInstance|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationPurchasableItemInstance|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationPurchasableItemInstance[]    findAll()
 * @method ApplicationPurchasableItemInstance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationPurchasableItemInstanceRepository extends AbstractRepository implements ApplicationPurchasableItemInstanceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationPurchasableItemInstance::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationPurchasableItemInstance(ApplicationPurchasableItemInstance $applicationPurchasableItemInstance, bool $flush): void
    {
        $this->save($applicationPurchasableItemInstance, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationPurchasableItemInstance(ApplicationPurchasableItemInstance $applicationPurchasableItemInstance, bool $flush): void
    {
        $this->remove($applicationPurchasableItemInstance, $flush);
    }
}