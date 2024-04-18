<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationPaymentSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method ApplicationPayment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationPayment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationPayment[]    findAll()
 * @method ApplicationPayment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationPaymentRepository extends AbstractRepository implements ApplicationPaymentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationPayment::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationPayment(ApplicationPayment $applicationPayment, bool $flush): void
    {
        $this->save($applicationPayment, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationPayment(ApplicationPayment $applicationPayment, bool $flush): void
    {
        $this->remove($applicationPayment, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?ApplicationPayment
    {
        return $this->createQueryBuilder('applicationPayment')
            ->select('applicationPayment, application, campDate, camp')
            ->leftJoin('applicationPayment.application', 'application')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('applicationPayment.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOneByExternalId(string $externalId): ?ApplicationPayment
    {
        return $this->createQueryBuilder('applicationPayment')
            ->select('applicationPayment, application, campDate, camp')
            ->leftJoin('applicationPayment.application', 'application')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('applicationPayment.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(ApplicationPaymentSearchData $data,
                                      Application                  $application,
                                      int                          $currentPage,
                                      int                          $pageSize): DqlPaginator
    {
        $type = $data->getType();
        $isOnline = $data->isOnline();
        $sortBy = $data->getSortBy();

        $queryBuilder = $this->createQueryBuilder('applicationPayment')
            ->select('applicationPayment, application, campDate, camp')
            ->leftJoin('applicationPayment.application', 'application')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('applicationPayment.application = :applicationId')
            ->setParameter('applicationId', $application->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($type !== null)
        {
            $queryBuilder
                ->andWhere('applicationPayment.type = :type')
                ->setParameter('type', $type->value)
            ;
        }

        if ($isOnline !== null)
        {
            $queryBuilder
                ->andWhere('applicationPayment.isOnline = :isOnline')
                ->setParameter('isOnline', $isOnline)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}