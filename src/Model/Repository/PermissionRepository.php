<?php

namespace App\Model\Repository;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
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
    public function createPermission(string $name, string $label, int $priority, PermissionGroup $group): Permission
    {
        return new Permission($name, $label, $priority, $group);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p, pg')
            ->leftJoin('p.group', 'pg')
            ->addOrderBy('pg.priority', 'ASC')
            ->addOrderBy('p.priority', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}