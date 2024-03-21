<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateUser;
use App\Model\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @method CampDateUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampDateUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampDateUser[]    findAll()
 * @method CampDateUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampDateUserRepository extends AbstractRepository implements CampDateUserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampDateUser::class);
    }

    /**
     * @inheritDoc
     */
    public function saveCampDateUser(CampDateUser $campDateUser, bool $flush): void
    {
        $this->save($campDateUser, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCampDateUser(CampDateUser $campDateUser, bool $flush): void
    {
        $this->remove($campDateUser, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findByCampDate(CampDate $campDate): array
    {
        return $this->createQueryBuilder('campDateUser')
            ->select('campDateUser, campDate, user')
            ->leftJoin('campDateUser.campDate', 'campDate')
            ->leftJoin('campDateUser.user', 'user')
            ->andWhere('campDateUser.campDate = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->addOrderBy('user.guidePriority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('campDateUser')
            ->select('campDateUser, campDate, user')
            ->leftJoin('campDateUser.campDate', 'campDate')
            ->leftJoin('campDateUser.user', 'user')
            ->andWhere('campDateUser.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->addOrderBy('campDate.startAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneForCampDateAndUser(CampDate $campDate, User $user): ?CampDateUser
    {
        return $this->createQueryBuilder('campDateUser')
            ->select('campDateUser, campDate, user')
            ->leftJoin('campDateUser.campDate', 'campDate')
            ->leftJoin('campDateUser.user', 'user')
            ->andWhere('campDateUser.campDate = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->andWhere('campDateUser.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}