<?php

namespace App\Model\Repository;

use App\Model\Entity\ImageContent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method ImageContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageContentRepository extends AbstractRepository implements ImageContentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageContent::class);
    }

    /**
     * @inheritDoc
     */
    public function saveImageContent(ImageContent $imageContent, bool $flush): void
    {
        $this->save($imageContent, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeImageContent(ImageContent $imageContent, bool $flush): void
    {
        $this->remove($imageContent, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('imageContent')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?ImageContent
    {
        return $this->createQueryBuilder('imageContent')
            ->andWhere('imageContent.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByIdentifier(string $identifier): ?ImageContent
    {
        return $this->createQueryBuilder('imageContent')
            ->andWhere('imageContent.identifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}