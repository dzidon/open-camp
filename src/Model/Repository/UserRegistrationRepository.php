<?php

namespace App\Model\Repository;

use App\Model\Entity\UserRegistration;
use App\Model\Enum\Entity\UserRegistrationStateEnum;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserRegistration>
 *
 * @method UserRegistration|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRegistration|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRegistration[]    findAll()
 * @method UserRegistration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRegistrationRepository extends AbstractRepository implements UserRegistrationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRegistration::class);
    }

    /**
     * @inheritDoc
     */
    public function saveUserRegistration(UserRegistration $userRegistration, bool $flush): void
    {
        $this->save($userRegistration, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeUserRegistration(UserRegistration $userRegistration, bool $flush): void
    {
        $this->remove($userRegistration, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneBySelector(string $selector, ?bool $active = null): ?UserRegistration
    {
        $queryBuilder = $this->createQueryBuilder('userRegistration');
        if ($active !== null)
        {
            $this->addActiveCondition($queryBuilder, $active);
        }

        return $queryBuilder
            ->andWhere('userRegistration.selector = :selector')
            ->setParameter('selector', $selector)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email, ?bool $active = null): array
    {
        $queryBuilder = $this->createQueryBuilder('userRegistration');
        if ($active !== null)
        {
            $this->addActiveCondition($queryBuilder, $active);
        }

        return $queryBuilder
            ->andWhere('userRegistration.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Adds a condition to a query builder to check if a user registration is active or not.
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
                ->andWhere(':now < userRegistration.expireAt')
                ->andWhere('userRegistration.state = :unusedState')
            ;
        }
        // is not active
        else
        {
            $queryBuilder
                ->orWhere('NOT (:now < userRegistration.expireAt)')
                ->orWhere('NOT (userRegistration.state = :unusedState)')
            ;
        }

        $queryBuilder->setParameter('now', new DateTimeImmutable('now'));
        $queryBuilder->setParameter('unusedState', UserRegistrationStateEnum::UNUSED->value);
    }
}
