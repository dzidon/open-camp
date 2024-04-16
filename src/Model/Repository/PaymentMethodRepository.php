<?php

namespace App\Model\Repository;

use App\Model\Entity\PaymentMethod;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentMethodRepository extends AbstractRepository implements PaymentMethodRepositoryInterface
{
    private array $enabledPaymentMethods;

    public function __construct(ManagerRegistry $registry, array $enabledPaymentMethods)
    {
        parent::__construct($registry, PaymentMethod::class);

        $this->enabledPaymentMethods = $enabledPaymentMethods;
    }

    /**
     * @inheritDoc
     */
    public function savePaymentMethod(PaymentMethod $paymentMethod, bool $flush): void
    {
        $this->save($paymentMethod, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removePaymentMethod(PaymentMethod $paymentMethod, bool $flush): void
    {
        $this->remove($paymentMethod, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(bool $enabledOnly = false, bool $includeBusinessMethods = true): array
    {
        $queryBuilder = $this->createQueryBuilder('paymentMethod')
            ->orderBy('paymentMethod.priority', 'DESC')
        ;

        if ($enabledOnly)
        {
            $queryBuilder
                ->andWhere('paymentMethod.name IN (:names)')
                ->setParameter('names', $this->enabledPaymentMethods)
            ;
        }

        if (!$includeBusinessMethods)
        {
            $queryBuilder->andWhere('paymentMethod.isForBusinessesOnly = FALSE');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }
}