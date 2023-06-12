<?php

namespace App\Repository;

use App\Entity\UserRegistration;
use App\Enum\Entity\UserRegistrationStateEnum;
use App\Security\TokenSplitterInterface;
use App\Security\UserRegistrationCreationResult;
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
    private int $maxActiveRegistrationsPerEmail;
    private string $registrationLifespan;

    private UserRepositoryInterface $userRepository;
    private TokenSplitterInterface $tokenSplitter;
    private PasswordHasherFactoryInterface $passwordHasher;

    public function __construct(ManagerRegistry $registry,
                                UserRepositoryInterface $userRepository,
                                TokenSplitterInterface $tokenSplitter,
                                PasswordHasherFactoryInterface $passwordHasher,
                                int $maxActiveRegistrationsPerEmail,
                                string $registrationLifespan)
    {
        parent::__construct($registry, UserRegistration::class);

        $this->userRepository = $userRepository;
        $this->tokenSplitter = $tokenSplitter;
        $this->passwordHasher = $passwordHasher;

        $this->maxActiveRegistrationsPerEmail = $maxActiveRegistrationsPerEmail;
        $this->registrationLifespan = $registrationLifespan;
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
    public function createUserRegistration(string $email): UserRegistrationCreationResult
    {
        $fake = false;

        // this email might be registered already
        if ($this->userRepository->isEmailRegistered($email))
        {
            $fake = true;
        }

        // maximum amount of active registrations might have been reached
        $activeRegistrations = $this->findByEmail($email, true);
        if (count($activeRegistrations) >= $this->maxActiveRegistrationsPerEmail)
        {
            $fake = true;
        }

        // make sure the selector is unique
        $selector = null;
        $plainVerifier = '';

        while ($selector === null || $this->findOneBySelector($selector) !== null)
        {
            $tokenSplit = $this->tokenSplitter->generateTokenSplit();
            $selector = $tokenSplit->getSelector();
            $plainVerifier = $tokenSplit->getPlainVerifier();
        }

        $hasher = $this->passwordHasher->getPasswordHasher(UserRegistration::class);
        $verifier = $hasher->hash($plainVerifier);

        $expireAt = new DateTimeImmutable(sprintf('+%s', $this->registrationLifespan));
        $userRegistration = new UserRegistration($email, $expireAt, $selector, $verifier);

        return new UserRegistrationCreationResult($userRegistration, $fake, $plainVerifier);
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
