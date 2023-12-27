<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateAttachmentConfig;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

class CampDateAttachmentConfigRepository extends AbstractRepository implements CampDateAttachmentConfigRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampDateAttachmentConfig::class);
    }

    /**
     * @inheritDoc
     */
    public function saveCampDateAttachmentConfig(CampDateAttachmentConfig $campDateAttachmentConfig, bool $flush): void
    {
        $this->save($campDateAttachmentConfig, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCampDateAttachmentConfig(CampDateAttachmentConfig $campDateAttachmentConfig, bool $flush): void
    {
        $this->remove($campDateAttachmentConfig, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findByCampDate(CampDate $campDate, ?bool $isGlobal = null): array
    {
        $queryBuilder = $this->createQueryBuilder('campDateAttachmentConfig')
            ->select('campDateAttachmentConfig, campDate, attachmentConfig, fileExtension')
            ->leftJoin('campDateAttachmentConfig.campDate', 'campDate')
            ->leftJoin('campDateAttachmentConfig.attachmentConfig', 'attachmentConfig')
            ->leftJoin('attachmentConfig.fileExtensions', 'fileExtension')
            ->andWhere('campDateAttachmentConfig.campDate = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->orderBy('campDateAttachmentConfig.priority', 'DESC')
        ;

        if ($isGlobal !== null)
        {
            $queryBuilder
                ->andWhere('attachmentConfig.isGlobal = :isGlobal')
                ->setParameter('isGlobal', $isGlobal)
            ;
        }

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }
}