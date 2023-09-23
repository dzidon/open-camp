<?php

namespace App\Model\Repository;

use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\FileExtension;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileExtension|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileExtension|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileExtension[]    findAll()
 * @method FileExtension[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileExtensionRepository extends AbstractRepository implements FileExtensionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileExtension::class);
    }

    /**
     * @inheritDoc
     */
    public function saveFileExtension(FileExtension $fileExtension, bool $flush): void
    {
        $this->save($fileExtension, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeFileExtension(FileExtension $fileExtension, bool $flush): void
    {
        $this->remove($fileExtension, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findForAttachmentConfigs(): array
    {
        return $this->_em->createQueryBuilder()
            ->select('fileExtension')
            ->from(FileExtension::class, 'fileExtension')
            ->join(AttachmentConfig::class, 'attachmentConfig', 'WITH', '1 = 1')
            ->join('attachmentConfig.fileExtensions', 'fileExtension2')
            ->andWhere('fileExtension.id = fileExtension2.id')
            ->orderBy('fileExtension.extension', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByExtension(string $extension): ?FileExtension
    {
        return $this->createQueryBuilder('fileExtension')
            ->andWhere('fileExtension.extension = :extension')
            ->setParameter('extension', $extension)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByExtensions(array $extensions): array
    {
        return $this->createQueryBuilder('fileExtension')
            ->andWhere('fileExtension.extension IN (:extensions)')
            ->setParameter('extensions', $extensions)
            ->orderBy('fileExtension.extension', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}