<?php

namespace App\Model\Repository;

use App\Form\DataTransfer\Data\Admin\RoleSearchDataInterface;
use App\Model\Entity\Role;
use App\Search\Paginator\DqlPaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 * @method Role[]    findAll()
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
    public function createRole(string $label): Role
    {
        return new Role($label);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Role
    {
        return $this->createQueryBuilder('role')
            ->select('role, rolePermission, rolePermissionGroup')
            ->leftJoin('role.permissions', 'rolePermission')
            ->leftJoin('rolePermission.group', 'rolePermissionGroup')
            ->andWhere('role.id = :id')
            ->setParameter('id', $id)
            ->addOrderBy('rolePermissionGroup.priority', 'ASC')
            ->addOrderBy('rolePermission.priority', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(RoleSearchDataInterface $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('role')
            ->andWhere('role.label LIKE :label')
            ->setParameter('label', '%' . $phrase . '%')
            ->orderBy('role.' . $sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query), $currentPage, $pageSize);
    }
}