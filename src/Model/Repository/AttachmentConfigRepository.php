<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\AttachmentConfigSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\FileExtension;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method AttachmentConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttachmentConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttachmentConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachmentConfigRepository extends AbstractRepository implements AttachmentConfigRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttachmentConfig::class);
    }

    /**
     * @inheritDoc
     */
    public function saveAttachmentConfig(AttachmentConfig $attachmentConfig, bool $flush): void
    {
        $this->save($attachmentConfig, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeAttachmentConfig(AttachmentConfig $attachmentConfig, bool $flush): void
    {
        $this->remove($attachmentConfig, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('attachmentConfig')
            ->select('attachmentConfig, fileExtension')
            ->leftJoin('attachmentConfig.fileExtensions', 'fileExtension')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?AttachmentConfig
    {
        return $this->createQueryBuilder('attachmentConfig')
            ->select('attachmentConfig, fileExtension')
            ->leftJoin('attachmentConfig.fileExtensions', 'fileExtension')
            ->andWhere('attachmentConfig.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByName(string $name): ?AttachmentConfig
    {
        return $this->createQueryBuilder('attachmentConfig')
            ->select('attachmentConfig, fileExtension')
            ->leftJoin('attachmentConfig.fileExtensions', 'fileExtension')
            ->andWhere('attachmentConfig.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(AttachmentConfigSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $requiredType = $data->getRequiredType();
        $fileExtensions = $data->getFileExtensions();
        $isGlobal = $data->isGlobal();

        $queryBuilder = $this->createQueryBuilder('attachmentConfig')
            ->select('DISTINCT attachmentConfig')
            ->andWhere('attachmentConfig.name LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($requiredType !== null)
        {
            $queryBuilder
                ->andWhere('attachmentConfig.requiredType = :requiredType')
                ->setParameter('requiredType', $requiredType->value)
            ;
        }

        if ($isGlobal !== null)
        {
            $queryBuilder
                ->andWhere('attachmentConfig.isGlobal = :isGlobal')
                ->setParameter('isGlobal', $isGlobal)
            ;
        }

        if (!empty($fileExtensions))
        {
            $fileExtensionIds = array_map(function (FileExtension $fileExtension) {
                return $fileExtension->getId()->toBinary();
            }, $fileExtensions);

            $queryBuilder
                ->leftJoin('attachmentConfig.fileExtensions', 'fileExtension')
                ->andWhere('fileExtension.id IN (:ids)')
                ->setParameter('ids', $fileExtensionIds)
            ;
        }

        $query = $queryBuilder->getQuery();
        $paginator = new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);

        // load file extensions
        $attachmentConfigs = $paginator->getCurrentPageItems();

        if (empty($attachmentConfigs))
        {
            return $paginator;
        }

        $attachmentConfigIds = array_map(function (AttachmentConfig $attachmentConfig) {
            return $attachmentConfig->getId()->toBinary();
        }, $attachmentConfigs);

        $this->createQueryBuilder('attachmentConfig')
            ->select('attachmentConfig, fileExtension')
            ->leftJoin('attachmentConfig.fileExtensions', 'fileExtension')
            ->andWhere('attachmentConfig.id IN (:ids)')
            ->setParameter('ids', $attachmentConfigIds)
            ->getQuery()
            ->getResult()
        ;

        return $paginator;
    }
}