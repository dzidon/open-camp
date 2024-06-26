<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\RoleSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Role;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoleRepository extends AbstractRepository implements RoleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @inheritDoc
     */
    public function saveRole(Role $role, bool $flush): void
    {
        $this->save($role, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeRole(Role $role, bool $flush): void
    {
        $this->remove($role, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('role')
            ->select('role, rolePermission, rolePermissionGroup')
            ->leftJoin('role.permissions', 'rolePermission')
            ->leftJoin('rolePermission.permissionGroup', 'rolePermissionGroup')
            ->addOrderBy('rolePermissionGroup.priority', 'DESC')
            ->addOrderBy('rolePermission.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?Role
    {
        return $this->createQueryBuilder('role')
            ->select('role, rolePermission, rolePermissionGroup')
            ->leftJoin('role.permissions', 'rolePermission')
            ->leftJoin('rolePermission.permissionGroup', 'rolePermissionGroup')
            ->andWhere('role.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->addOrderBy('rolePermissionGroup.priority', 'DESC')
            ->addOrderBy('rolePermission.priority', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByLabel(string $label): ?Role
    {
        return $this->createQueryBuilder('role')
            ->select('role, rolePermission, rolePermissionGroup')
            ->leftJoin('role.permissions', 'rolePermission')
            ->leftJoin('rolePermission.permissionGroup', 'rolePermissionGroup')
            ->andWhere('role.label = :label')
            ->setParameter('label', $label)
            ->addOrderBy('rolePermissionGroup.priority', 'DESC')
            ->addOrderBy('rolePermission.priority', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(RoleSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('role')
            ->andWhere('role.label LIKE :label')
            ->setParameter('label', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}