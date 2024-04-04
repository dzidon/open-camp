<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPaymentStateConfig;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApplicationPaymentStateConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationPaymentStateConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationPaymentStateConfig[]    findAll()
 * @method ApplicationPaymentStateConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationPaymentStateConfigRepository extends AbstractRepository implements ApplicationPaymentStateConfigRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationPaymentStateConfig::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationPaymentStateConfig(ApplicationPaymentStateConfig $applicationPaymentStateConfig, bool $flush): void
    {
        $this->save($applicationPaymentStateConfig, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationPaymentStateConfig(ApplicationPaymentStateConfig $applicationPaymentStateConfig, bool $flush): void
    {
        $this->remove($applicationPaymentStateConfig, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneByConfiguration(array $states,
                                           array $paidStates,
                                           array $cancelledStates,
                                           array $refundedStates,
                                           array $pendingStates,
                                           array $validStateChanges): ?ApplicationPaymentStateConfig
    {
        return $this->createQueryBuilder('applicationPaymentStateConfig')
            ->andWhere('applicationPaymentStateConfig.states = :states')
            ->setParameter('states', $states, Types::JSON)
            ->andWhere('applicationPaymentStateConfig.paidStates = :paidStates')
            ->setParameter('paidStates', $paidStates, Types::JSON)
            ->andWhere('applicationPaymentStateConfig.cancelledStates = :cancelledStates')
            ->setParameter('cancelledStates', $cancelledStates, Types::JSON)
            ->andWhere('applicationPaymentStateConfig.refundedStates = :refundedStates')
            ->setParameter('refundedStates', $refundedStates, Types::JSON)
            ->andWhere('applicationPaymentStateConfig.pendingStates = :pendingStates')
            ->setParameter('pendingStates', $pendingStates, Types::JSON)
            ->andWhere('applicationPaymentStateConfig.validStateChanges = :validStateChanges')
            ->setParameter('validStateChanges', $validStateChanges, Types::JSON)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}