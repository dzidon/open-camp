<?php

namespace App\Repository;

use App\Entity\UserRegistration;
use App\Enum\Entity\UserRegistrationStateEnum;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

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
    private PasswordHasherFactoryInterface $passwordHasher;

    public function __construct(ManagerRegistry $registry, PasswordHasherFactoryInterface $passwordHasher)
    {
        parent::__construct($registry, UserRegistration::class);

        $this->passwordHasher = $passwordHasher;
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
    public function createUserRegistration(string $email,
                                           DateTimeImmutable $expireAt,
                                           string $selector,
                                           string $plainVerifier): UserRegistration
    {
        $hasher = $this->passwordHasher->getPasswordHasher(UserRegistration::class);
        $verifier = $hasher->hash($plainVerifier);

        return new UserRegistration($email, $expireAt, $selector, $verifier);
    }

    /**
     * @inheritDoc
     */
    public function findOneBySelector(string $selector, ?bool $active = null): ?UserRegistration
    {
        $queryBuilder = $this->createQueryBuilder('ur');
        if ($active !== null)
        {
            $this->addActiveCondition($queryBuilder, $active);
        }

        return $queryBuilder
            ->andWhere('ur.selector = :selector')
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
        $queryBuilder = $this->createQueryBuilder('ur');
        if ($active !== null)
        {
            $this->addActiveCondition($queryBuilder, $active);
        }

        return $queryBuilder
            ->andWhere('ur.email = :email')
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
                ->andWhere(':now < ur.expireAt')
                ->andWhere('ur.state = :unusedState')
            ;
        }
        // is not active
        else
        {
            $queryBuilder
                ->orWhere('NOT (:now < ur.expireAt)')
                ->orWhere('NOT (ur.state = :unusedState)')
            ;
        }

        $queryBuilder->setParameter('now', new DateTimeImmutable('now'));
        $queryBuilder->setParameter('unusedState', UserRegistrationStateEnum::UNUSED->value);
    }
}
