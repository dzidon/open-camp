<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\UserSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateUser;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
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
class UserRepository extends AbstractRepository implements UserRepositoryInterface, UserLoaderInterface, UserProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
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
    public function findOneById(UuidV4 $id): ?User
    {
        return $this->createQueryBuilder('user')
            ->select('user, userRole, userRolePermission, userRolePermissionGroup')
            ->leftJoin('user.role', 'userRole')
            ->leftJoin('userRole.permissions', 'userRolePermission')
            ->leftJoin('userRolePermission.permissionGroup', 'userRolePermissionGroup')
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
            ->leftJoin('userRolePermission.permissionGroup', 'userRolePermissionGroup')
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
    public function findOneByUrlName(string $urlName): ?User
    {
        return $this->createQueryBuilder('user')
            ->select('user, userRole, userRolePermission, userRolePermissionGroup')
            ->leftJoin('user.role', 'userRole')
            ->leftJoin('userRole.permissions', 'userRolePermission')
            ->leftJoin('userRolePermission.permissionGroup', 'userRolePermissionGroup')
            ->andWhere('user.urlName = :urlName')
            ->setParameter('urlName', $urlName)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findThoseWithNotNullUrlNames(): array
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.urlName IS NOT NULL')
            ->addOrderBy('user.guidePriority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByRole(?Role $role): array
    {
        $queryBuilder = $this->createQueryBuilder('user');

        if ($role === null)
        {
            $queryBuilder->andWhere('user.role IS NULL');
        }
        else
        {
            $queryBuilder
                ->andWhere('user.role = :roleId')
                ->setParameter('roleId', $role->getId(), UuidType::NAME)
            ;
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByCampDates(array $campDates, bool $withUrlNameOnly = false): array
    {
        $campDateIds = array_map(function (CampDate $campDate) {
            return $campDate->getId()->toBinary();
        }, $campDates);

        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('DISTINCT user')
            ->from(User::class, 'user')
            ->leftJoin(CampDateUser::class, 'campDateUser', 'WITH', 'campDateUser.user = user.id')
            ->andWhere('campDateUser.campDate IN (:campDateIds)')
            ->setParameter('campDateIds', $campDateIds)
            ->addOrderBy('user.guidePriority', 'DESC')
        ;

        if ($withUrlNameOnly)
        {
            $queryBuilder->andWhere('user.urlName IS NOT NULL');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function existsAtLeastOneGuideWithUrlName(): bool
    {
        $count = $this->createQueryBuilder('user')
            ->select('COUNT(user.id)')
            ->andWhere('user.urlName IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @inheritDoc
     */
    public function getUserGuidePaginator(bool $isFeaturedOnly, int $currentPage, int $pageSize): DqlPaginator
    {
        $queryBuilder = $this->createQueryBuilder('user')
            ->andWhere('user.urlName IS NOT NULL')
            ->orderBy('user.guidePriority', 'DESC')
        ;

        if ($isFeaturedOnly)
        {
            $queryBuilder->andWhere('user.isFeaturedGuide = TRUE');
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(UserSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $role = $data->getRole();
        $isFeaturedGuide = $data->isFeaturedGuide();

        $queryBuilder = $this->createQueryBuilder('user')
            ->select('user, userRole')
            ->leftJoin('user.role', 'userRole')
            ->orWhere('user.email LIKE :email')
            ->setParameter('email', '%' . $phrase . '%')
            ->orWhere('user.urlName LIKE :urlName')
            ->setParameter('urlName', '%' . $phrase . '%')
            ->orWhere('CONCAT(user.nameFirst, \' \', user.nameLast) LIKE :fullName')
            ->setParameter('fullName', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($role === false)
        {
            $queryBuilder->andWhere('user.role IS NULL');
        }
        else if ($role !== null)
        {
            $queryBuilder
                ->andWhere('user.role = :roleId')
                ->setParameter('roleId', $role->getId(), UuidType::NAME)
            ;
        }

        if ($isFeaturedGuide !== null)
        {
            $queryBuilder
                ->andWhere('user.isFeaturedGuide = :isFeaturedGuide')
                ->setParameter('isFeaturedGuide', $isFeaturedGuide)
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