<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationCampDateSearchData;
use App\Library\Data\Admin\CampDateSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\User;
use App\Model\Library\Camp\UserUpcomingCampDatesResult;
use App\Model\Library\CampDate\AdminApplicationCampDatesResult;
use DateTimeInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;
use Symfony\Component\Uid\UuidV4;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @method CampDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampDateRepository extends AbstractRepository implements CampDateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampDate::class);
    }

    /**
     * @inheritDoc
     */
    public function saveCampDate(CampDate $campDate, bool $flush): void
    {
        $this->save($campDate, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCampDate(CampDate $campDate, bool $flush): void
    {
        $this->remove($campDate, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?CampDate
    {
        $campDate = $this->createQueryBuilder('campDate')
            ->select('campDate, camp, campCategory, tripLocationPathThere, tripLocationPathBack, campDateUser, user')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('camp.campCategory', 'campCategory')
            ->leftJoin('campDate.tripLocationPathThere', 'tripLocationPathThere')
            ->leftJoin('campDate.tripLocationPathBack', 'tripLocationPathBack')
            ->leftJoin('campDate.campDateUsers', 'campDateUser')
            ->leftJoin('campDateUser.user', 'user')
            ->andWhere('campDate.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->addOrderBy('user.guidePriority', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->loadCampDateFormFields($campDate);
        $this->loadCampDateAttachmentConfigs($campDate);
        $this->loadCampDatePurchasableItems($campDate);

        return $campDate;
    }

    /**
     * @inheritDoc
     */
    public function findUpcomingByCamp(Camp $camp, bool $showHidden = true): UserUpcomingCampDatesResult
    {
        // camp dates

        $queryBuilder = $this->createQueryBuilder('campDate')
            ->select('campDate, camp, campCategory, tripLocationPathThere, tripLocationPathBack, campDateUser, user')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('camp.campCategory', 'campCategory')
            ->leftJoin('campDate.tripLocationPathThere', 'tripLocationPathThere')
            ->leftJoin('campDate.tripLocationPathBack', 'tripLocationPathBack')
            ->leftJoin('campDate.campDateUsers', 'campDateUser')
            ->leftJoin('campDateUser.user', 'user')
            ->andWhere('campDate.camp = :campId')
            ->setParameter('campId', $camp->getId(), UuidType::NAME)
            ->andWhere('campDate.startAt > :now')
            ->setParameter('now', new DateTimeImmutable('now'))
            ->addOrderBy('campDate.startAt', 'ASC')
            ->addOrderBy('user.guidePriority', 'DESC')
        ;

        if (!$showHidden)
        {
            $queryBuilder->andWhere('campDate.isHidden = FALSE');
        }

        $campDates = $queryBuilder
            ->getQuery()
            ->getResult()
        ;

        $this->loadCampDateFormFields($campDates);
        $this->loadCampDateAttachmentConfigs($campDates);
        $this->loadCampDatePurchasableItems($campDates);

        // determining which camp dates are open

        $campDateIds = $this->getCampDateIds($campDates);

        $queryBuilder = $this->createQueryBuilder('campDate')
            ->leftJoin(Application::class, 'application', 'WITH', '
                campDate.id = application.campDate AND
                application.isDraft = FALSE AND
                (application.isAccepted IS NULL OR application.isAccepted = TRUE)
            ')
            ->leftJoin(ApplicationCamper::class, 'applicationCamper', 'WITH', '
                application.id = applicationCamper.application
            ')
            ->andWhere('campDate.id IN (:campDateIds)')
            ->setParameter('campDateIds', $campDateIds)
            ->andWhere('campDate.isClosed = FALSE')
            ->andHaving('(campDate.isOpenAboveCapacity = TRUE OR COUNT(applicationCamper.id) < campDate.capacity)')
            ->addGroupBy('campDate.id, campDate.isOpenAboveCapacity, campDate.capacity')
            ->orderBy('campDate.startAt', 'ASC')
        ;

        if (!$showHidden)
        {
            $queryBuilder->andWhere('campDate.isHidden = FALSE');
        }

        $openCampDates = $queryBuilder
            ->getQuery()
            ->getResult()
        ;

        return new UserUpcomingCampDatesResult($campDates, $openCampDates);
    }

    /**
     * @inheritDoc
     */
    public function isCampDateOpenForApplications(CampDate $campDate): bool
    {
        $result = (int) $this->createQueryBuilder('campDate')
            ->select('COUNT(campDate.id)')
            ->leftJoin(Application::class, 'application', 'WITH', '
                campDate.id = application.campDate AND
                application.isDraft = FALSE AND
                (application.isAccepted IS NULL OR application.isAccepted = TRUE)
            ')
            ->leftJoin(ApplicationCamper::class, 'applicationCamper', 'WITH', '
                application.id = applicationCamper.application
            ')
            ->andWhere('campDate.id = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->andWhere('campDate.isClosed = FALSE')
            ->andWhere('campDate.startAt > :now')
            ->setParameter('now', new DateTimeImmutable('now'))
            ->andHaving('(campDate.isOpenAboveCapacity = TRUE OR COUNT(applicationCamper.id) < campDate.capacity)')
            ->addGroupBy('campDate.id, campDate.isOpenAboveCapacity, campDate.capacity')
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR)
        ;

        return $result > 0;
    }

    /**
     * @inheritDoc
     */
    public function findThoseThatCollideWithInterval(?Camp $camp, DateTimeInterface $startAt, DateTimeInterface $endAt): array
    {
        $queryBuilder = $this->createQueryBuilder('campDate')
            ->select('campDate, camp, tripLocationPathThere, tripLocationPathBack, campDateUser, user')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('campDate.tripLocationPathThere', 'tripLocationPathThere')
            ->leftJoin('campDate.tripLocationPathBack', 'tripLocationPathBack')
            ->leftJoin('campDate.campDateUsers', 'campDateUser')
            ->leftJoin('campDateUser.user', 'user')
            ->andWhere('
                (:startAt >= campDate.startAt AND :startAt <= campDate.endAt) OR
                (:endAt   >= campDate.startAt AND :endAt <= campDate.endAt)   OR
                (campDate.startAt >= :startAt AND campDate.endAt <= :endAt)
            ')
            ->setParameter('startAt', $startAt)
            ->setParameter('endAt', $endAt)
            ->addOrderBy('campDate.startAt', 'ASC')
            ->addOrderBy('user.guidePriority', 'DESC')
        ;

        if ($camp !== null)
        {
            $queryBuilder
                ->andWhere('camp.id = :id')
                ->setParameter('id', $camp->getId(), UuidType::NAME)
            ;
        }

        $campDates = $queryBuilder
            ->getQuery()
            ->getResult()
        ;

        $this->loadCampDateFormFields($campDates);
        $this->loadCampDateAttachmentConfigs($campDates);
        $this->loadCampDatePurchasableItems($campDates);

        return $campDates;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(CampDateSearchData $data, Camp $camp, int $currentPage, int $pageSize): DqlPaginator
    {
        $from = $data->getFrom();
        $to = $data->getTo();
        $sortBy = $data->getSortBy();
        $isHistorical = $data->isHistorical();
        $isHidden = $data->isHidden();
        $isOpenOnly = $data->isOpenOnly();

        $queryBuilder = $this->createQueryBuilder('campDate')
            ->andWhere('campDate.camp = :id')
            ->setParameter('id', $camp->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($from !== null)
        {
            $queryBuilder
                ->andWhere('campDate.startAt >= :from')
                ->setParameter('from', $from->format('Y-m-d 00:00:00'))
            ;
        }

        if ($to !== null)
        {
            $queryBuilder
                ->andWhere('campDate.endAt <= :to')
                ->setParameter('to', $to->format('Y-m-d 23:59:59'))
            ;
        }

        if ($isHidden !== null)
        {
            $queryBuilder
                ->andWhere('campDate.isHidden = :isHidden')
                ->setParameter('isHidden', $isHidden)
            ;
        }

        if ($isHistorical === true)
        {
            $queryBuilder
                ->andWhere('campDate.endAt < :now')
                ->setParameter('now', new DateTimeImmutable('now'))
            ;
        }
        else if ($isHistorical === false)
        {
            $queryBuilder
                ->andWhere('campDate.endAt >= :now')
                ->setParameter('now', new DateTimeImmutable('now'))
            ;
        }

        if ($isOpenOnly === true)
        {
            $queryBuilder
                ->leftJoin(Application::class, 'application', 'WITH', '
                    campDate.id = application.campDate AND 
                    application.isDraft = FALSE AND
                    (application.isAccepted IS NULL OR application.isAccepted = TRUE)
                ')
                ->leftJoin(ApplicationCamper::class, 'applicationCamper', 'WITH', '
                    application.id = applicationCamper.application
                ')
                ->andWhere('campDate.isClosed = FALSE')
                ->andWhere('campDate.startAt > :now')
                ->setParameter('now', new DateTimeImmutable('now'))
                ->andHaving('(campDate.isOpenAboveCapacity = TRUE OR COUNT(applicationCamper.id) < campDate.capacity)')
                ->addGroupBy('campDate.id, campDate.isOpenAboveCapacity, campDate.capacity')
            ;
        }
        else if ($isOpenOnly === false)
        {
            $openCampDateIdsResult = $this->createQueryBuilder('openCampDate')
                ->select('DISTINCT openCampDate.id')
                ->leftJoin(Application::class, 'otherApplication', 'WITH', '
                    openCampDate.id = otherApplication.campDate AND 
                    otherApplication.isDraft = FALSE AND
                    (otherApplication.isAccepted IS NULL OR otherApplication.isAccepted = TRUE)
                ')
                ->leftJoin(ApplicationCamper::class, 'otherApplicationCamper', 'WITH', '
                    otherApplication.id = otherApplicationCamper.application
                ')
                ->andWhere('openCampDate.isClosed = FALSE')
                ->andWhere('openCampDate.startAt > :nowInSubQuery')
                ->setParameter('nowInSubQuery', new DateTimeImmutable('now'))
                ->andHaving('(openCampDate.isOpenAboveCapacity = TRUE OR COUNT(otherApplicationCamper.id) < openCampDate.capacity)')
                ->addGroupBy('openCampDate.id, openCampDate.isOpenAboveCapacity, openCampDate.capacity')
                ->getQuery()
                ->getArrayResult()
            ;

            $openCampDateIds = array_column($openCampDateIdsResult, 'id');
            $openCampDateIdsBinary = array_map(function (UuidV4 $id) {
                return $id->toBinary();
            }, $openCampDateIds);

            $queryBuilder
                ->andWhere('campDate.id NOT IN (:openCampDateIds)')
                ->setParameter('openCampDateIds', $openCampDateIdsBinary)
            ;
        }

        $query = $queryBuilder->getQuery();

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }

    /**
     * @inheritDoc
     */
    public function getAdminApplicationCampDatesResult(ApplicationCampDateSearchData $data,
                                                       Camp                          $camp,
                                                       ?User                         $guide,
                                                       int                           $currentPage,
                                                       int                           $pageSize): AdminApplicationCampDatesResult
    {
        // paginator

        $sortBy = $data->getSortBy();
        $from = $data->getFrom();
        $to = $data->getTo();

        $queryBuilder = $this->createQueryBuilder('campDate')
            ->select('campDate, COUNT(application.id) AS HIDDEN numberOfPendingApplications')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin(Application::class, 'application', 'WITH', '
                campDate.id = application.campDate AND
                application.isDraft = FALSE AND
                application.isAccepted IS NULL
            ')
            ->andWhere('campDate.camp = :campId')
            ->setParameter('campId', $camp->getId(), UuidType::NAME)
            ->groupBy('campDate.id')
            ->orderBy($sortBy->property(), $sortBy->order())
        ;

        if ($from !== null)
        {
            $queryBuilder
                ->andWhere('campDate.startAt >= :from')
                ->setParameter('from', $from->format('Y-m-d 00:00:00'))
            ;
        }

        if ($to !== null)
        {
            $queryBuilder
                ->andWhere('campDate.endAt <= :to')
                ->setParameter('to', $to->format('Y-m-d 23:59:59'))
            ;
        }

        if ($guide !== null)
        {
            $queryBuilder
                ->leftJoin('campDate.campDateUsers', 'campDateUser')
                ->andWhere('campDateUser.user = :guideId')
                ->setParameter('guideId', $guide->getId(), UuidType::NAME)
            ;
        }

        $query = $queryBuilder->getQuery();
        $paginator = new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);

        // numbers of pending applications

        $campDates = $paginator->getCurrentPageItems();
        $campDateBinaryIds = array_map(function (CampDate $campDate) {
            return $campDate->getId()->toBinary();
        }, $campDates);

        $queryResult = $this->createQueryBuilder('campDate')
            ->select('campDate.id, COUNT(application.id) AS numberOfPendingApplications')
            ->leftJoin(Application::class, 'application', 'WITH', '
                campDate.id = application.campDate AND
                application.isDraft = FALSE AND
                application.isAccepted IS NULL
            ')
            ->andWhere('campDate.id IN (:ids)')
            ->setParameter('ids', $campDateBinaryIds)
            ->groupBy('campDate.id')
            ->getQuery()
            ->getArrayResult()
        ;

        $numbersOfPendingApplications = [];

        foreach ($queryResult as $data)
        {
            /** @var UuidV4 $campDateId */
            $campDateId = $data['id'];
            $campDateIdString = $campDateId->toRfc4122();
            $numbersOfPendingApplications[$campDateIdString] = $data['numberOfPendingApplications'];
        }

        // number of accepted application campers

        $queryResult = $this->createQueryBuilder('campDate')
            ->select('campDate.id, COUNT(applicationCamper.id) AS numberOfAcceptedApplicationCampers')
            ->leftJoin(Application::class, 'application', 'WITH', '
                campDate.id = application.campDate AND
                application.isDraft = FALSE AND
                application.isAccepted = TRUE
            ')
            ->leftJoin(ApplicationCamper::class, 'applicationCamper', 'WITH', '
                application.id = applicationCamper.application
            ')
            ->andWhere('campDate.id IN (:ids)')
            ->setParameter('ids', $campDateBinaryIds)
            ->groupBy('campDate.id')
            ->getQuery()
            ->getArrayResult()
        ;

        $numberOfAcceptedApplicationCampers = [];

        foreach ($queryResult as $data)
        {
            /** @var UuidV4 $campDateId */
            $campDateId = $data['id'];
            $campDateIdString = $campDateId->toRfc4122();
            $numberOfAcceptedApplicationCampers[$campDateIdString] = $data['numberOfAcceptedApplicationCampers'];
        }

        return new AdminApplicationCampDatesResult($paginator, $numbersOfPendingApplications, $numberOfAcceptedApplicationCampers);
    }

    private function loadCampDateFormFields(null|array|CampDate $campDates): void
    {
        if ($campDates === null)
        {
            return;
        }

        $campDateIds = $this->getCampDateIds($campDates);

        $this->createQueryBuilder('campDate')
            ->select('campDate, campDateFormField, formField')
            ->leftJoin('campDate.campDateFormFields', 'campDateFormField')
            ->leftJoin('campDateFormField.formField', 'formField')
            ->andWhere('campDate.id IN (:ids)')
            ->setParameter('ids', $campDateIds)
            ->orderBy('campDateFormField.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadCampDateAttachmentConfigs(null|array|CampDate $campDates): void
    {
        if ($campDates === null)
        {
            return;
        }

        $campDateIds = $this->getCampDateIds($campDates);

        $this->createQueryBuilder('campDate')
            ->select('campDate, campDateAttachmentConfig, attachmentConfig, fileExtension')
            ->leftJoin('campDate.campDateAttachmentConfigs', 'campDateAttachmentConfig')
            ->leftJoin('campDateAttachmentConfig.attachmentConfig', 'attachmentConfig')
            ->leftJoin('attachmentConfig.fileExtensions', 'fileExtension')
            ->andWhere('campDate.id IN (:ids)')
            ->setParameter('ids', $campDateIds)
            ->orderBy('campDateAttachmentConfig.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadCampDatePurchasableItems(null|array|CampDate $campDates): void
    {
        if ($campDates === null)
        {
            return;
        }

        $campDateIds = $this->getCampDateIds($campDates);

        /** @var CampDate[] $campDates */
        $campDates = $this->createQueryBuilder('campDate')
            ->select('campDate, campDatePurchasableItem, purchasableItem')
            ->leftJoin('campDate.campDatePurchasableItems', 'campDatePurchasableItem')
            ->leftJoin('campDatePurchasableItem.purchasableItem', 'purchasableItem')
            ->andWhere('campDate.id IN (:ids)')
            ->setParameter('ids', $campDateIds)
            ->orderBy('campDatePurchasableItem.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $purchasableItems = [];

        foreach ($campDates as $campDate)
        {
            foreach ($campDate->getCampDatePurchasableItems() as $campDatePurchasableItem)
            {
                $purchasableItems[] = $campDatePurchasableItem->getPurchasableItem();
            }
        }

        $this->loadPurchasableItemVariants($purchasableItems);
    }

    private function loadPurchasableItemVariants(null|array|PurchasableItem $purchasableItems): void
    {
        if (empty($purchasableItems))
        {
            return;
        }

        $purchasableItemIds = $this->getPurchasableItemIds($purchasableItems);

        /** @var PurchasableItem[] $purchasableItems */
        $purchasableItems = $this->_em->createQueryBuilder()
            ->select('purchasableItem, purchasableItemVariant')
            ->from(PurchasableItem::class, 'purchasableItem')
            ->leftJoin('purchasableItem.purchasableItemVariants', 'purchasableItemVariant')
            ->andWhere('purchasableItem.id IN (:ids)')
            ->setParameter('ids', $purchasableItemIds)
            ->orderBy('purchasableItemVariant.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $purchasableItemVariants = [];

        foreach ($purchasableItems as $purchasableItem)
        {
            foreach ($purchasableItem->getPurchasableItemVariants() as $purchasableItemVariant)
            {
                $purchasableItemVariants[] = $purchasableItemVariant;
            }
        }

        $this->loadPurchasableItemVariantValues($purchasableItemVariants);
    }

    private function loadPurchasableItemVariantValues(null|array|PurchasableItemVariant $purchasableItemVariants): void
    {
        if (empty($purchasableItemVariants))
        {
            return;
        }

        $purchasableItemVariantIds = $this->getPurchasableItemVariantIds($purchasableItemVariants);

        $this->_em->createQueryBuilder()
            ->select('purchasableItemVariant, purchasableItemVariantValue')
            ->from(PurchasableItemVariant::class, 'purchasableItemVariant')
            ->leftJoin('purchasableItemVariant.purchasableItemVariantValues', 'purchasableItemVariantValue')
            ->andWhere('purchasableItemVariant.id IN (:ids)')
            ->setParameter('ids', $purchasableItemVariantIds)
            ->orderBy('purchasableItemVariantValue.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function getPurchasableItemVariantIds(array|PurchasableItemVariant $purchasableItemVariants): array
    {
        if ($purchasableItemVariants instanceof PurchasableItemVariant)
        {
            $purchasableItemVariants = [$purchasableItemVariants];
        }

        return array_map(function (PurchasableItemVariant $purchasableItemVariant) {
            return $purchasableItemVariant->getId()->toBinary();
        }, $purchasableItemVariants);
    }

    private function getPurchasableItemIds(array|PurchasableItem $purchasableItems): array
    {
        if ($purchasableItems instanceof PurchasableItem)
        {
            $purchasableItems = [$purchasableItems];
        }

        return array_map(function (PurchasableItem $purchasableItem) {
            return $purchasableItem->getId()->toBinary();
        }, $purchasableItems);
    }

    private function getCampDateIds(array|CampDate $campDates): array
    {
        if ($campDates instanceof CampDate)
        {
            $campDates = [$campDates];
        }

        return array_map(function (CampDate $campDate) {
            return $campDate->getId()->toBinary();
        }, $campDates);
    }
}