<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\DownloadableFileSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\DownloadableFile;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method DownloadableFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method DownloadableFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method DownloadableFile[]    findAll()
 * @method DownloadableFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DownloadableFileRepository extends AbstractRepository implements DownloadableFileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DownloadableFile::class);
    }

    /**
     * @inheritDoc
     */
    public function saveDownloadableFile(DownloadableFile $downloadableFile, bool $flush): void
    {
        $this->save($downloadableFile, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeDownloadableFile(DownloadableFile $downloadableFile, bool $flush): void
    {
        $this->remove($downloadableFile, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?DownloadableFile
    {
        return $this->createQueryBuilder('downloadableFile')
            ->andWhere('downloadableFile.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findUsedExtensions(): array
    {
        $arrayResult = $this->createQueryBuilder('downloadableFile')
            ->select('DISTINCT downloadableFile.extension')
            ->orderBy('downloadableFile.extension', 'ASC')
            ->getQuery()
            ->getArrayResult()
        ;

        return array_column($arrayResult, 'extension');
    }

    /**
     * @inheritDoc
     */
    public function getUserGuidePaginator(int $currentPage, int $pageSize): DqlPaginator
    {
        $query = $this->createQueryBuilder('downloadableFile')
            ->orderBy('downloadableFile.priority', 'DESC')
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(DownloadableFileSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $extensions = $data->getExtensions();

        $queryBuilder = $this->createQueryBuilder('downloadableFile')
            ->andWhere('downloadableFile.title LIKE :title')
            ->setParameter('title', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if (!empty($extensions))
        {
            $queryBuilder
                ->andWhere('downloadableFile.extension IN (:extensions)')
                ->setParameter('extensions', $extensions)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}