<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PageSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Page;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends AbstractRepository implements PageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * @inheritDoc
     */
    public function savePage(Page $page, bool $flush): void
    {
        $this->save($page, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removePage(Page $page, bool $flush): void
    {
        $this->remove($page, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('page')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?Page
    {
        return $this->createQueryBuilder('page')
            ->andWhere('page.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByUrlName(string $urlName): ?Page
    {
        return $this->createQueryBuilder('page')
            ->andWhere('page.urlName = :urlName')
            ->setParameter('urlName', $urlName)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(PageSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $isHidden = $data->isHidden();

        $queryBuilder = $this->createQueryBuilder('page')
            ->andWhere('page.title LIKE :title')
            ->setParameter('title', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($isHidden !== null)
        {
            $queryBuilder
                ->andWhere('page.isHidden = :isHidden')
                ->setParameter('isHidden', $isHidden)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}