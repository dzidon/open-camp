<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationFormFieldValue;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApplicationFormFieldValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationFormFieldValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationFormFieldValue[]    findAll()
 * @method ApplicationFormFieldValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationFormFieldValueRepository extends AbstractRepository implements ApplicationFormFieldValueRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationFormFieldValue::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationFormFieldValue(ApplicationFormFieldValue $applicationFormFieldValue, bool $flush): void
    {
        $this->save($applicationFormFieldValue, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationFormFieldValue(ApplicationFormFieldValue $applicationFormFieldValue, bool $flush): void
    {
        $this->remove($applicationFormFieldValue, $flush);
    }
}