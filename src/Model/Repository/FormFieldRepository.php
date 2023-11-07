<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\FormFieldSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\FormField;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method FormField|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormField|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormFieldRepository extends AbstractRepository implements FormFieldRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormField::class);
    }

    /**
     * @inheritDoc
     */
    public function saveFormField(FormField $formField, bool $flush): void
    {
        $this->save($formField, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeFormField(FormField $formField, bool $flush): void
    {
        $this->remove($formField, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('formField')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?FormField
    {
        return $this->createQueryBuilder('formField')
            ->andWhere('formField.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?FormField
    {
        return $this->createQueryBuilder('formField')
            ->andWhere('formField.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(FormFieldSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $type = $data->getType();
        $isRequired = $data->isRequired();
        $isGlobal = $data->isGlobal();

        $queryBuilder = $this->createQueryBuilder('formField')
            ->andWhere('formField.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($type !== null)
        {
            $queryBuilder
                ->andWhere('formField.type = :type')
                ->setParameter('type', $type->value)
            ;
        }

        if ($isRequired !== null)
        {
            $queryBuilder
                ->andWhere('formField.isRequired = :isRequired')
                ->setParameter('isRequired', $isRequired)
            ;
        }

        if ($isGlobal !== null)
        {
            $queryBuilder
                ->andWhere('formField.isGlobal = :isGlobal')
                ->setParameter('isGlobal', $isGlobal)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}