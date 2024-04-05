<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationCamperSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationAcceptedStateEnum;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\CampDate;
use DateTimeImmutable;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method ApplicationCamper|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationCamper|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationCamper[]    findAll()
 * @method ApplicationCamper[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationCamperRepository extends AbstractRepository implements ApplicationCamperRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationCamper::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationCamper(ApplicationCamper $applicationCamper, bool $flush): void
    {
        $this->save($applicationCamper, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationCamper(ApplicationCamper $applicationCamper, bool $flush): void
    {
        $this->remove($applicationCamper, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?ApplicationCamper
    {
        $applicationCamper = $this->createQueryBuilder('applicationCamper')
            ->select('applicationCamper, application, campDate, camp, applicationTripLocationPath')
            ->leftJoin('applicationCamper.application', 'application')
            ->leftJoin('applicationCamper.applicationTripLocationPaths', 'applicationTripLocationPath')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('applicationCamper.id = :applicationCamperId')
            ->setParameter('applicationCamperId', $id, UuidType::NAME)
            ->addOrderBy('applicationTripLocationPath.isThere', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->loadApplicationCamperAttachments($applicationCamper);
        $this->loadApplicationCamperFormFieldValues($applicationCamper);
        $this->loadApplicationCamperPurchasableItemInstances($applicationCamper);

        return $applicationCamper;
    }

    /**
     * @inheritDoc
     */
    public function findThoseThatOccupySlotsInCampDate(CampDate $campDate): array
    {
        $applicationCampers = $this->createQueryBuilder('applicationCamper')
            ->select('applicationCamper, application, campDate, applicationTripLocationPath')
            ->leftJoin('applicationCamper.application', 'application')
            ->leftJoin('applicationCamper.applicationTripLocationPaths', 'applicationTripLocationPath')
            ->leftJoin('application.campDate', 'campDate')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('(application.isAccepted IS NULL OR application.isAccepted = TRUE)')
            ->andWhere('campDate.id = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->addOrderBy('applicationTripLocationPath.isThere', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $this->loadApplicationCamperAttachments($applicationCampers);
        $this->loadApplicationCamperFormFieldValues($applicationCampers);
        $this->loadApplicationCamperPurchasableItemInstances($applicationCampers);

        return $applicationCampers;
    }



    /**
     * @inheritDoc
     */
    public function findAcceptedByCampDate(CampDate $campDate): array
    {
        $applicationCampers = $this->createQueryBuilder('applicationCamper')
            ->select('applicationCamper, application, campDate, applicationTripLocationPath')
            ->leftJoin('applicationCamper.application', 'application')
            ->leftJoin('applicationCamper.applicationTripLocationPaths', 'applicationTripLocationPath')
            ->leftJoin('application.campDate', 'campDate')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('application.isAccepted = TRUE')
            ->andWhere('campDate.id = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->addOrderBy('applicationCamper.nameLast', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $this->loadApplicationCamperAttachments($applicationCampers);
        $this->loadApplicationCamperFormFieldValues($applicationCampers);
        $this->loadApplicationCamperPurchasableItemInstances($applicationCampers);

        return $applicationCampers;
    }

    /**
     * @inheritDoc
     */
    public function getNumberOfOtherCompleteAcceptedApplications(ApplicationCamper $applicationCamper): int
    {
        $application = $applicationCamper->getApplication();
        $applicationId = $application->getId();
        $user = $application->getUser();

        if ($user === null)
        {
            return 0;
        }

        $userId = $user->getId();
        $nameFirst = $applicationCamper->getNameFirst();
        $nameLast = $applicationCamper->getNameLast();
        $bornAt = $applicationCamper->getBornAt();
        $gender = $applicationCamper->getGender();

        return $this->createQueryBuilder('applicationCamper')
            ->select('count(DISTINCT applicationCamper.id)')
            ->leftJoin('applicationCamper.application', 'application')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('application.isAccepted = TRUE')
            ->andWhere('application.id != :applicationId')
            ->setParameter('applicationId', $applicationId, UuidType::NAME)
            ->andWhere('application.user = :userId')
            ->setParameter('userId', $userId, UuidType::NAME)
            ->andWhere('applicationCamper.nameFirst = :nameFirst')
            ->setParameter('nameFirst', $nameFirst)
            ->andWhere('applicationCamper.nameLast = :nameLast')
            ->setParameter('nameLast', $nameLast)
            ->andWhere('applicationCamper.bornAt = :bornAt')
            ->setParameter('bornAt', $bornAt)
            ->andWhere('applicationCamper.gender = :gender')
            ->setParameter('gender', $gender->value)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getNumberOfAcceptedApplicationCampersForCampDate(CampDate $campDate): int
    {
        return (int) $this->createQueryBuilder('applicationCamper')
            ->select('COUNT(applicationCamper.id)')
            ->leftJoin('applicationCamper.application', 'application')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('application.isAccepted = TRUE')
            ->andWhere('application.campDate = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(ApplicationCamperSearchData $data,
                                      Application|CampDate        $applicationOrCampDate,
                                      int                         $currentPage,
                                      int                         $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $ageMin = $data->getAgeMin();
        $ageMax = $data->getAgeMax();
        $gender = $data->getGender();
        $isApplicationAccepted = $data->getIsApplicationAccepted();
        $isEnabledApplicationAcceptedSearch = $data->isEnabledApplicationAcceptedSearch();

        $queryBuilder = $this->createQueryBuilder('applicationCamper')
            ->select('applicationCamper, application')
            ->leftJoin('applicationCamper.application', 'application')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('CONCAT(applicationCamper.nameFirst, \' \', applicationCamper.nameLast) LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($applicationOrCampDate instanceof Application)
        {
            $application = $applicationOrCampDate;

            $queryBuilder
                ->andWhere('application.id = :applicationId')
                ->setParameter('applicationId', $application->getId(), UuidType::NAME)
            ;
        }
        else
        {
            $campDate = $applicationOrCampDate;

            $queryBuilder
                ->andWhere('application.campDate = :campDateId')
                ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ;
        }

        if ($ageMin !== null)
        {
            $maxDateTime = new DateTimeImmutable(sprintf('-%s years', $ageMin));

            $queryBuilder
                ->andWhere('applicationCamper.bornAt <= :maxDateTime')
                ->setParameter('maxDateTime', $maxDateTime)
            ;
        }

        if ($ageMax !== null)
        {
            $ageMax++;
            $minDateTime = new DateTimeImmutable(sprintf('-%s years', $ageMax));

            $queryBuilder
                ->andWhere('applicationCamper.bornAt > :minDateTime')
                ->setParameter('minDateTime', $minDateTime)
            ;
        }

        if ($gender !== null)
        {
            $queryBuilder
                ->andWhere('applicationCamper.gender = :gender')
                ->setParameter('gender', $gender->value)
            ;
        }

        if ($isEnabledApplicationAcceptedSearch && $isApplicationAccepted !== null)
        {
            if ($isApplicationAccepted === ApplicationAcceptedStateEnum::ACCEPTED)
            {
                $queryBuilder->andWhere('application.isAccepted = TRUE');
            }
            else if ($isApplicationAccepted === ApplicationAcceptedStateEnum::DECLINED)
            {
                $queryBuilder->andWhere('application.isAccepted = FALSE');
            }
            else if ($isApplicationAccepted === ApplicationAcceptedStateEnum::UNSETTLED)
            {
                $queryBuilder->andWhere('application.isAccepted IS NULL');
            }
        }

        $query = $queryBuilder->getQuery();
        $paginator = new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);

        /** @var ApplicationCamper[] $applicationCampers */
        $applicationCampers = $paginator->getCurrentPageItems();

        $this->loadApplicationCamperTripLocationPaths($applicationCampers);
        $this->loadApplicationCamperPurchasableItemInstances($applicationCampers);

        return $paginator;
    }

    private function loadApplicationCamperTripLocationPaths(null|array|ApplicationCamper $applicationCampers): void
    {
        if (empty($applicationCampers))
        {
            return;
        }

        $applicationCamperIds = $this->getApplicationCamperIds($applicationCampers);

        $this->createQueryBuilder('applicationCamper')
            ->select('applicationCamper, applicationTripLocationPath')
            ->leftJoin('applicationCamper.applicationTripLocationPaths', 'applicationTripLocationPath')
            ->andWhere('applicationCamper.id IN (:ids)')
            ->setParameter('ids', $applicationCamperIds)
            ->addOrderBy('applicationTripLocationPath.isThere', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationCamperFormFieldValues(null|array|ApplicationCamper $applicationCampers): void
    {
        if (empty($applicationCampers))
        {
            return;
        }

        $applicationCamperIds = $this->getApplicationCamperIds($applicationCampers);

        $this->createQueryBuilder('applicationCamper')
            ->select('applicationCamper, applicationFormFieldValue')
            ->leftJoin('applicationCamper.applicationFormFieldValues', 'applicationFormFieldValue')
            ->andWhere('applicationCamper.id IN (:ids)')
            ->setParameter('ids', $applicationCamperIds)
            ->orderBy('applicationFormFieldValue.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationCamperAttachments(null|array|ApplicationCamper $applicationCampers): void
    {
        if (empty($applicationCampers))
        {
            return;
        }

        $applicationCamperIds = $this->getApplicationCamperIds($applicationCampers);

        $this->createQueryBuilder('applicationCamper')
            ->select('applicationCamper, applicationAttachment')
            ->leftJoin('applicationCamper.applicationAttachments', 'applicationAttachment')
            ->andWhere('applicationCamper.id IN (:ids)')
            ->setParameter('ids', $applicationCamperIds)
            ->orderBy('applicationAttachment.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationCamperPurchasableItemInstances(null|array|ApplicationCamper $applicationCampers): void
    {
        if (empty($applicationCampers))
        {
            return;
        }

        $applicationCamperIds = $this->getApplicationCamperIds($applicationCampers);

        $this->createQueryBuilder('applicationCamper')
            ->select('applicationCamper, applicationPurchasableItemInstance')
            ->leftJoin('applicationCamper.applicationPurchasableItemInstances', 'applicationPurchasableItemInstance')
            ->andWhere('applicationCamper.id IN (:ids)')
            ->setParameter('ids', $applicationCamperIds)
            ->orderBy('applicationPurchasableItemInstance.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function getApplicationCamperIds(array|ApplicationCamper $applicationCampers): array
    {
        if ($applicationCampers instanceof ApplicationCamper)
        {
            $applicationCampers = [$applicationCampers];
        }

        return array_map(function (ApplicationCamper $applicationCamper) {
            return $applicationCamper->getId()->toBinary();
        }, $applicationCampers);
    }
}