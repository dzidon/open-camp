<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationAttachment;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method ApplicationAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationAttachment[]    findAll()
 * @method ApplicationAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationAttachmentRepository extends AbstractRepository implements ApplicationAttachmentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationAttachment::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationAttachment(ApplicationAttachment $applicationAttachment, bool $flush): void
    {
        $this->save($applicationAttachment, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationAttachment(ApplicationAttachment $applicationAttachment, bool $flush): void
    {
        $this->remove($applicationAttachment, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?ApplicationAttachment
    {
        return $this->createQueryBuilder('applicationAttachment')
            ->select('applicationAttachment, application, applicationCamper, applicationCamperApplication')
            ->leftJoin('applicationAttachment.application', 'application')
            ->leftJoin('applicationAttachment.applicationCamper', 'applicationCamper')
            ->leftJoin('applicationCamper.application', 'applicationCamperApplication')
            ->andWhere('applicationAttachment.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}