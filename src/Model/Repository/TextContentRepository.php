<?php

namespace App\Model\Repository;

use App\Model\Entity\TextContent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method TextContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method TextContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method TextContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TextContentRepository extends AbstractRepository implements TextContentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TextContent::class);
    }

    /**
     * @inheritDoc
     */
    public function saveTextContent(TextContent $textContent, bool $flush): void
    {
        $this->save($textContent, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeTextContent(TextContent $textContent, bool $flush): void
    {
        $this->remove($textContent, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('textContent')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?TextContent
    {
        return $this->createQueryBuilder('textContent')
            ->andWhere('textContent.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByIdentifier(string $identifier): ?TextContent
    {
        return $this->createQueryBuilder('textContent')
            ->andWhere('textContent.identifier = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}