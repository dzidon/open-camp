<?php

namespace App\Model\Repository;

use App\Model\Entity\Permission;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends AbstractRepository implements PermissionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    /**
     * @inheritDoc
     */
    public function savePermission(Permission $permission, bool $flush): void
    {
        $this->save($permission, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removePermission(Permission $permission, bool $flush): void
    {
        $this->remove($permission, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('permission')
            ->select('permission, permissionGroup')
            ->leftJoin('permission.permissionGroup', 'permissionGroup')
            ->addOrderBy('permissionGroup.priority', 'ASC')
            ->addOrderBy('permission.priority', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}