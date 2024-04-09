<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationAdminAttachmentSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationAdminAttachment;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method ApplicationAdminAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationAdminAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationAdminAttachment[]    findAll()
 * @method ApplicationAdminAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationAdminAttachmentRepository extends AbstractRepository implements ApplicationAdminAttachmentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationAdminAttachment::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationAdminAttachment(ApplicationAdminAttachment $applicationAdminAttachment, bool $flush): void
    {
        $this->save($applicationAdminAttachment, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationAdminAttachment(ApplicationAdminAttachment $applicationAdminAttachment, bool $flush): void
    {
        $this->remove($applicationAdminAttachment, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?ApplicationAdminAttachment
    {
        return $this->createQueryBuilder('applicationAdminAttachment')
            ->select('applicationAdminAttachment, application, campDate, camp')
            ->leftJoin('applicationAdminAttachment.application', 'application')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('applicationAdminAttachment.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(ApplicationAdminAttachmentSearchData $data,
                                      Application                          $application,
                                      int                                  $currentPage,
                                      int                                  $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $extensions = $data->getExtensions();

        $queryBuilder = $this->createQueryBuilder('applicationAdminAttachment')
            ->andWhere('applicationAdminAttachment.application = :applicationId')
            ->setParameter('applicationId', $application->getId(), UuidType::NAME)
            ->andWhere('applicationAdminAttachment.label LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if (!empty($extensions))
        {
            $queryBuilder
                ->andWhere('applicationAdminAttachment.extension IN (:extensions)')
                ->setParameter('extensions', $extensions)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}