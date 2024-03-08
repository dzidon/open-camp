<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPayment;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApplicationPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationPayment[]    findAll()
 * @method ApplicationPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationPaymentRepository extends AbstractRepository implements ApplicationPaymentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationPayment::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationPayment(ApplicationPayment $applicationPayment, bool $flush): void
    {
        $this->save($applicationPayment, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationPayment(ApplicationPayment $applicationPayment, bool $flush): void
    {
        $this->remove($applicationPayment, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId): ?ApplicationPayment
    {
        return $this->createQueryBuilder('applicationPayment')
            ->select('applicationPayment, applicationPaymentStateConfig')
            ->leftJoin('applicationPayment.applicationPaymentStateConfig', 'applicationPaymentStateConfig')
            ->andWhere('applicationPayment.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}