<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPaymentStateConfig;
use App\Model\Event\User\ApplicationPaymentStateConfig\ApplicationPaymentStateConfigCreateEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @method ApplicationPaymentStateConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationPaymentStateConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationPaymentStateConfig[]    findAll()
 * @method ApplicationPaymentStateConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationPaymentStateConfigRepository extends AbstractRepository implements ApplicationPaymentStateConfigRepositoryInterface
{
    /** @var ApplicationPaymentStateConfig[] */
    private array $newlyCreated = [];

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ManagerRegistry $registry, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($registry, ApplicationPaymentStateConfig::class);

        $this->eventDispatcher = $eventDispatcher;
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
    public function findOneByConfigurationOrCreateNew(array $states,
                                                      array $paidStates,
                                                      array $cancelledStates,
                                                      array $refundedStates,
                                                      array $pendingStates,
                                                      array $validStateChanges): ApplicationPaymentStateConfig
    {
        $existingPaymentConfig = $this->createQueryBuilder('applicationPaymentStateConfig')
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

        if ($existingPaymentConfig !== null)
        {
            return $existingPaymentConfig;
        }

        foreach ($this->newlyCreated as $stateConfig)
        {
            if ($stateConfig->getStates()            === $states          &&
                $stateConfig->getPaidStates()        === $paidStates      &&
                $stateConfig->getCancelledStates()   === $cancelledStates &&
                $stateConfig->getRefundedStates()    === $refundedStates  &&
                $stateConfig->getPendingStates()     === $pendingStates   &&
                $stateConfig->getValidStateChanges() === $validStateChanges)
            {
                return $stateConfig;
            }
        }

        $event = new ApplicationPaymentStateConfigCreateEvent(
            $states,
            $paidStates,
            $cancelledStates,
            $refundedStates,
            $pendingStates,
            $validStateChanges,
        );

        $event->setIsFlush(false);
        $this->eventDispatcher->dispatch($event, $event::NAME);
        $newStateConfig = $event->getApplicationPaymentStateConfig();
        $this->newlyCreated[] = $newStateConfig;

        return $newStateConfig;
    }
}