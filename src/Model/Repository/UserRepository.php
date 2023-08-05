<?php

namespace App\Model\Repository;

use App\Form\DataTransfer\Data\Admin\UserSearchDataInterface;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use App\Search\Paginator\DqlPaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Uid\UuidV4;

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
    public function findOneById(UuidV4 $id): ?User
    {
        return $this->createQueryBuilder('user')
            ->select('user, userRole, userRolePermission, userRolePermissionGroup')
            ->leftJoin('user.role', 'userRole')
            ->leftJoin('userRole.permissions', 'userRolePermission')
            ->leftJoin('userRolePermission.group', 'userRolePermissionGroup')
            ->andWhere('user.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('user')
            ->select('user, userRole, userRolePermission, userRolePermissionGroup')
            ->leftJoin('user.role', 'userRole')
            ->leftJoin('userRole.permissions', 'userRolePermission')
            ->leftJoin('userRolePermission.group', 'userRolePermissionGroup')
            ->andWhere('user.email = :email')
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
        $count = $this->createQueryBuilder('user')
            ->select('count(user.id)')
            ->andWhere('user.email = :email')
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
        return $this->createQueryBuilder('user')
            ->andWhere('user.role = :roleId')
            ->setParameter('roleId', $role->getId(), UuidType::NAME)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(UserSearchDataInterface $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $role = $data->getRole();

        $queryBuilder = $this->createQueryBuilder('user')
            ->select('user, userRole')
            ->leftJoin('user.role', 'userRole')
            ->orWhere('user.email LIKE :email')
            ->setParameter('email', '%' . $phrase . '%')
            ->orWhere('CONCAT(user.nameFirst, \' \', user.nameLast) LIKE :fullName')
            ->setParameter('fullName', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($role !== null)
        {
            $queryBuilder
                ->andWhere('user.role = :roleId')
                ->setParameter('roleId', $role->getId(), UuidType::NAME)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
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