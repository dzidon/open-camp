<?php

namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Model\Entity\UserPasswordChange;
use App\Model\Enum\Entity\UserPasswordChangeStateEnum;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @extends ServiceEntityRepository<UserPasswordChange>
 *
 * @method UserPasswordChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPasswordChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPasswordChange[]    findAll()
 * @method UserPasswordChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPasswordChangeRepository extends AbstractRepository implements UserPasswordChangeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPasswordChange::class);
    }

    /**
     * @inheritDoc
     */
    public function saveUserPasswordChange(UserPasswordChange $userPasswordChange, bool $flush): void
    {
        $this->save($userPasswordChange, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeUserPasswordChange(UserPasswordChange $userPasswordChange, bool $flush): void
    {
        $this->remove($userPasswordChange, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneBySelector(string $selector, ?bool $active = null): ?UserPasswordChange
    {
        $queryBuilder = $this->createQueryBuilder('userPasswordChange');
        if ($active !== null)
        {
            $this->addActiveCondition($queryBuilder, $active);
        }

        return $queryBuilder
            ->select('userPasswordChange, userPasswordChangeUser')
            ->leftJoin('userPasswordChange.user', 'userPasswordChangeUser')
            ->andWhere('userPasswordChange.selector = :selector')
            ->setParameter('selector', $selector)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByUser(User $user, ?bool $active = null): array
    {
        $queryBuilder = $this->createQueryBuilder('userPasswordChange');
        if ($active !== null)
        {
            $this->addActiveCondition($queryBuilder, $active);
        }

        return $queryBuilder
            ->select('userPasswordChange, userPasswordChangeUser')
            ->leftJoin('userPasswordChange.user', 'userPasswordChangeUser')
            ->andWhere('userPasswordChange.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Adds a condition to a query builder to check if a user password change is active or not.
     *
     * @param QueryBuilder $queryBuilder
     * @param bool $positive
     * @return void
     */
    private function addActiveCondition(QueryBuilder $queryBuilder, bool $positive): void
    {
        // is active
        if ($positive)
        {
            $queryBuilder
                ->andWhere('userPasswordChange.user IS NOT NULL')
                ->andWhere(':now < userPasswordChange.expireAt')
                ->andWhere('userPasswordChange.state = :unusedState')
            ;
        }
        // is not active
        else
        {
            $queryBuilder
                ->orWhere('userPasswordChange.user IS NULL')
                ->orWhere('NOT (:now < userPasswordChange.expireAt)')
                ->orWhere('NOT (userPasswordChange.state = :unusedState)')
            ;
        }

        $queryBuilder->setParameter('now', new DateTimeImmutable('now'));
        $queryBuilder->setParameter('unusedState', UserPasswordChangeStateEnum::UNUSED->value);
    }
}
