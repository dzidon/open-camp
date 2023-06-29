<?php

namespace App\Repository;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends AbstractRepository implements UserRepositoryInterface,
                                                           UserLoaderInterface,
                                                           UserProviderInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);

        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @inheritDoc
     */
    public function saveUser(User $user, bool $flush): void
    {
        $this->save($user, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeUser(User $user, bool $flush): void
    {
        $this->remove($user, $flush);
    }

    /**
     * @inheritDoc
     */
    public function createUser(string $email, ?string $plainPassword = null): User
    {
        $user = new User($email);

        if ($plainPassword !== null)
        {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u, ur, urp, urpg')
            ->leftJoin('u.role', 'ur')
            ->leftJoin('ur.permissions', 'urp')
            ->leftJoin('urp.group', 'urpg')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function isEmailRegistered(string $email): bool
    {
        $count = $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @inheritDoc
     */
    public function findByRole(?Role $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->findOneByEmail($identifier);

        if ($user === null)
        {
            $notFoundMessage = sprintf('User with e-mail "%s" was not found.', $identifier);
            $notFoundException = new UserNotFoundException($notFoundMessage);
            $notFoundException->setUserIdentifier($identifier);

            throw $notFoundException;
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User)
        {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_debug_type($user))
            );
        }

        $identifier = $user->getUserIdentifier();
        return $this->loadUserByIdentifier($identifier);
    }
}