<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateFormField;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @method CampDateFormField|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampDateFormField|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampDateFormField[]    findAll()
 * @method CampDateFormField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampDateFormFieldRepository extends AbstractRepository implements CampDateFormFieldRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampDateFormField::class);
    }

    /**
     * @inheritDoc
     */
    public function saveCampDateFormField(CampDateFormField $campDateFormField, bool $flush): void
    {
        $this->save($campDateFormField, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCampDateFormField(CampDateFormField $campDateFormField, bool $flush): void
    {
        $this->remove($campDateFormField, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findByCampDate(CampDate $campDate): array
    {
        return $this->createQueryBuilder('campDateFormField')
            ->select('campDateFormField, campDate, formField')
            ->leftJoin('campDateFormField.campDate', 'campDate')
            ->leftJoin('campDateFormField.formField', 'formField')
            ->andWhere('campDateFormField.campDate = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->getQuery()
            ->getResult()
        ;
    }
}