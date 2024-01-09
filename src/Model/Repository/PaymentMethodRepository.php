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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentMethod::class);
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
    public function findAll(): array
    {
        return $this->createQueryBuilder('paymentMethod')
            ->orderBy('paymentMethod.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}