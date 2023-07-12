<?php

namespace App\Model\Repository;

use App\Model\Entity\PermissionGroup;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PermissionGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method PermissionGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method PermissionGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionGroupRepository extends AbstractRepository implements PermissionGroupRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PermissionGroup::class);
    }

    /**
     * @inheritDoc
     */
    public function savePermissionGroup(PermissionGroup $permissionGroup, bool $flush): void
    {
        $this->save($permissionGroup, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removePermissionGroup(PermissionGroup $permissionGroup, bool $flush): void
    {
        $this->remove($permissionGroup, $flush);
    }

    /**
     * @inheritDoc
     */
    public function createPermissionGroup(string $name, string $label, int $priority): PermissionGroup
    {
        return new PermissionGroup($name, $label, $priority);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('permissionGroup')
            ->getQuery()
            ->getResult()
        ;
    }
}