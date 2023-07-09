<?php

namespace App\Repository;

use App\Entity\Role;
use App\Form\DataTransfer\Data\Admin\RoleSearchDataInterface;
use App\Search\Paginator\DqlPaginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

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
        return $this->createQueryBuilder('r')
            ->select('r, rp, rpg')
            ->leftJoin('r.permissions', 'rp')
            ->leftJoin('rp.group', 'rpg')
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->addOrderBy('rpg.priority', 'ASC')
            ->addOrderBy('rp.priority', 'ASC')
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

        $query = $this->createQueryBuilder('r')
            ->andWhere('r.label LIKE :label')
            ->setParameter('label', '%' . $phrase . '%')
            ->orderBy('r.' . $sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query), $currentPage, $pageSize);
    }
}