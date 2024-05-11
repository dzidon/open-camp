<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationSearchData;
use App\Library\Data\User\ApplicationProfileSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationAcceptedStateEnum;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Library\Application\ApplicationsEditableDraftsResult;
use App\Model\Library\Application\ApplicationTotalRevenueResult;
use DateTimeImmutable;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Uid\UuidV4;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends AbstractRepository implements ApplicationRepositoryInterface
{
    private RequestStack $requestStack;

    private string $currency;

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(
        ManagerRegistry $registry,
        RequestStack    $requestStack,

        #[Autowire('%app.currency%')]
        string $currency,

        #[Autowire('%app.last_completed_application_id_session_key%')]
        string $lastCompletedApplicationIdSessionKey
    ) {
        parent::__construct($registry, Application::class);

        $this->requestStack = $requestStack;
        $this->currency = $currency;
        $this->lastCompletedApplicationIdSessionKey = $lastCompletedApplicationIdSessionKey;
    }

    /**
     * @inheritDoc
     */
    public function saveApplication(Application $application, bool $flush): void
    {
        $this->save($application, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplication(Application $application, bool $flush): void
    {
        $this->remove($application, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?Application
    {
        $application = $this->createQueryBuilder('application')
            ->select('application, campDate, user, paymentMethod, camp, campDateUser, guideUser')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('application.user', 'user')
            ->leftJoin('application.paymentMethod', 'paymentMethod')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('campDate.campDateUsers', 'campDateUser')
            ->leftJoin('campDateUser.user', 'guideUser')
            ->andWhere('application.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->addOrderBy('user.guidePriority', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->loadApplicationContacts($application);
        $this->loadApplicationCampers($application);
        $this->loadApplicationAttachments($application);
        $this->loadApplicationFormFieldValues($application);
        $this->loadApplicationPayments($application);
        $this->loadApplicationPurchasableItems($application);
        $this->loadApplicationAdminAttachments($application);

        return $application;
    }

    /**
     * @inheritDoc
     */
    public function findLastCompletedFromSession(): ?Application
    {
        $session = $this->getSession();
        $applicationIdString = $session->get($this->lastCompletedApplicationIdSessionKey);

        if ($applicationIdString === null || !UuidV4::isValid($applicationIdString))
        {
            return null;
        }

        $applicationId = UuidV4::fromString($applicationIdString);

        return $this->findOneById($applicationId);
    }

    /**
     * @inheritDoc
     */
    public function findOneBySimpleId(string $simpleId): ?Application
    {
        /** @var Application|null $application */
        $application = $this->createQueryBuilder('application')
            ->select('application, campDate, user, paymentMethod, camp, leader')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('application.user', 'user')
            ->leftJoin('application.paymentMethod', 'paymentMethod')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('campDate.leaders', 'leader')
            ->andWhere('application.simpleId = :simpleId')
            ->setParameter('simpleId', $simpleId)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->loadApplicationContacts($application);
        $this->loadApplicationCampers($application);
        $this->loadApplicationAttachments($application);
        $this->loadApplicationFormFieldValues($application);
        $this->loadApplicationPayments($application);
        $this->loadApplicationPurchasableItems($application);
        $this->loadApplicationAdminAttachments($application);

        return $application;
    }

    /**
     * @inheritDoc
     */
    public function simpleIdExists(string $simpleId): bool
    {
        $count = $this->createQueryBuilder('application')
            ->select('count(application.id)')
            ->andWhere('application.simpleId = :simpleId')
            ->setParameter('simpleId', $simpleId)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @inheritDoc
     */
    public function getHighestInvoiceNumber(): ?int
    {
        return $this->createQueryBuilder('application')
            ->select('max(application.invoiceNumber)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getApplicationsEditableDraftsResult(array $applications): ApplicationsEditableDraftsResult
    {
        $applicationIds = [];
        $applicationBinaryIds = [];

        foreach ($applications as $application)
        {
            $applicationId = $application;

            if ($application instanceof Application)
            {
                $applicationId = $application->getId();
            }

            $applicationIds[] = $applicationId;
            $applicationBinaryIds[] = $applicationId->toBinary();
        }

        $result = $this->createQueryBuilder('application')
            ->select('application.id')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin(Application::class, 'otherApplication', 'WITH', '
                campDate.id = otherApplication.campDate AND
                otherApplication.isDraft = FALSE AND
                (otherApplication.isAccepted IS NULL OR otherApplication.isAccepted = TRUE)
            ')
            ->leftJoin(ApplicationCamper::class, 'otherApplicationCamper', 'WITH', '
                otherApplication.id = otherApplicationCamper.application
            ')
            ->andWhere('application.campDate IS NOT NULL')
            ->andWhere('application.isDraft = TRUE')
            ->andWhere('application.id IN (:applicationIds)')
            ->setParameter('applicationIds', $applicationBinaryIds)
            ->andWhere('campDate.isClosed = FALSE')
            ->andWhere('campDate.startAt > :now')
            ->setParameter('now', new DateTimeImmutable('now'))
            ->andHaving('(campDate.isOpenAboveCapacity = TRUE OR COUNT(otherApplicationCamper.id) < campDate.capacity)')
            ->addGroupBy('application.id, campDate.isOpenAboveCapacity, campDate.capacity')
            ->getQuery()
            ->getArrayResult()
        ;

        $activeApplicationIds = array_column($result, 'id');
        $isApplicationEditableDraft = [];

        foreach ($applicationIds as $applicationId)
        {
            $applicationIdString = $applicationId->toRfc4122();
            $isApplicationEditableDraft[$applicationIdString] = in_array($applicationId, $activeApplicationIds);
        }

        return new ApplicationsEditableDraftsResult($isApplicationEditableDraft);
    }

    /**
     * @inheritDoc
     */
    public function getUserPaginator(ApplicationProfileSearchData $data, User $user, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('application')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('application.simpleId LIKE :simpleId')
            ->setParameter('simpleId', '%' . $phrase . '%')
            ->andWhere('application.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        $paginator = new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);

        /** @var Application[] $applications */
        $applications = $paginator->getCurrentPageItems();
        $this->loadApplicationContacts($applications);
        $this->loadApplicationCampers($applications);
        $this->loadApplicationAttachments($applications);
        $this->loadApplicationFormFieldValues($applications);
        $this->loadApplicationPayments($applications);
        $this->loadApplicationPurchasableItems($applications);
        $this->loadApplicationAdminAttachments($applications);

        return $paginator;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(ApplicationSearchData $data,
                                      null|User|CampDate    $guideOrCampDate,
                                      int                   $currentPage,
                                      int                   $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();
        $isAccepted = $data->getIsAccepted();
        $isOnlinePaymentMethod = $data->isOnlinePaymentMethod();
        $invoiceNumberParameter = -1;

        if (is_numeric($phrase))
        {
            $invoiceNumberParameter = ltrim($phrase, '0');
        }

        $queryBuilder = $this->createQueryBuilder('application')
            ->select('application, paymentMethod')
            ->leftJoin('application.paymentMethod', 'paymentMethod')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('(
                application.simpleId      LIKE :phrase        OR
                application.email         LIKE :phrase        OR
                application.invoiceNumber =    :invoiceNumber
            )')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->setParameter('invoiceNumber', $invoiceNumberParameter)
            ->orderBy($sortBy->property(), $sortBy->order())
            ->groupBy('application.id')
        ;

        if ($isOnlinePaymentMethod !== null)
        {
            $queryBuilder
                ->andWhere('paymentMethod.isOnline = :isOnline')
                ->setParameter('isOnline', $isOnlinePaymentMethod)
            ;
        }

        if ($isAccepted === ApplicationAcceptedStateEnum::ACCEPTED)
        {
            $queryBuilder->andWhere('application.isAccepted = TRUE');
        }
        else if ($isAccepted === ApplicationAcceptedStateEnum::DECLINED)
        {
            $queryBuilder->andWhere('application.isAccepted = FALSE');
        }
        else if ($isAccepted === ApplicationAcceptedStateEnum::UNSETTLED)
        {
            $queryBuilder->andWhere('application.isAccepted IS NULL');
        }

        if ($guideOrCampDate instanceof User)
        {
            $guide = $guideOrCampDate;

            $queryBuilder
                ->leftJoin('application.campDate', 'campDate')
                ->leftJoin('campDate.campDateUsers', 'campDateUser')
                ->andWhere('campDateUser.user = :guideId')
                ->setParameter('guideId', $guide->getId(), UuidType::NAME)
            ;
        }
        else if ($guideOrCampDate instanceof CampDate)
        {
            $campDate = $guideOrCampDate;

            $queryBuilder
                ->andWhere('application.campDate = :campDateId')
                ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ;
        }

        $query = $queryBuilder->getQuery();
        $paginator = new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);

        /** @var Application[] $applications */
        $applications = $paginator->getCurrentPageItems();
        $this->loadApplicationContacts($applications);
        $this->loadApplicationCampers($applications);
        $this->loadApplicationAttachments($applications);
        $this->loadApplicationFormFieldValues($applications);
        $this->loadApplicationPayments($applications);
        $this->loadApplicationPurchasableItems($applications);
        $this->loadApplicationAdminAttachments($applications);

        return $paginator;
    }

    /**
     * @inheritDoc
     */
    public function getTotalRevenueForCampResult(Camp $camp): ApplicationTotalRevenueResult
    {
        $arrayResult = $this->createQueryBuilder('application')
            ->select('application.currency, SUM(application.fullPriceCached) AS fullPrice')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('application.isAccepted = TRUE')
            ->andWhere('camp.id = :campId')
            ->setParameter('campId', $camp->getId(), UuidType::NAME)
            ->groupBy('application.currency')
            ->orderBy('application.completedAt', 'DESC')
            ->getQuery()
            ->getArrayResult()
        ;

        $locale = $this->getLocale();
        $totalsByCurrency = [];

        foreach ($arrayResult as $data)
        {
            $currency = $data['currency'];
            $total = $data['fullPrice'];
            $totalsByCurrency[$currency] = $total;
        }

        return new ApplicationTotalRevenueResult($this->currency, $locale, $totalsByCurrency);
    }

    /**
     * @inheritDoc
     */
    public function getNumberOfAcceptedApplicationsForCamp(Camp $camp): int
    {
        return (int) $this->createQueryBuilder('application')
            ->select('COUNT(application.id)')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('application.isAccepted = TRUE')
            ->andWhere('camp.id = :campId')
            ->setParameter('campId', $camp->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getTotalRevenueForCampDateResult(CampDate $campDate): ApplicationTotalRevenueResult
    {
        $arrayResult = $this->createQueryBuilder('application')
            ->select('application.currency AS currency, SUM(application.fullPriceCached) AS fullPrice')
            ->leftJoin('application.campDate', 'campDate')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('application.isAccepted = TRUE')
            ->andWhere('campDate.id = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->groupBy('application.currency')
            ->orderBy('application.completedAt', 'DESC')
            ->getQuery()
            ->getArrayResult()
        ;

        $locale = $this->getLocale();
        $totalsByCurrency = [];

        foreach ($arrayResult as $data)
        {
            $currency = $data['currency'];
            $total = $data['fullPrice'];
            $totalsByCurrency[$currency] = $total;
        }

        return new ApplicationTotalRevenueResult($this->currency, $locale, $totalsByCurrency);
    }

    /**
     * @inheritDoc
     */
    public function getNumberOfAcceptedApplicationsForCampDate(CampDate $campDate): int
    {
        return (int) $this->createQueryBuilder('application')
            ->select('COUNT(application.id)')
            ->leftJoin('application.campDate', 'campDate')
            ->andWhere('application.isDraft = FALSE')
            ->andWhere('application.isAccepted = TRUE')
            ->andWhere('campDate.id = :campDateId')
            ->setParameter('campDateId', $campDate->getId(), UuidType::NAME)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    private function loadApplicationContacts(null|array|Application $applications): void
    {
        if (empty($applications))
        {
            return;
        }

        $applicationIds = $this->getApplicationIds($applications);

        $this->createQueryBuilder('application')
            ->select('application, applicationContact')
            ->leftJoin('application.applicationContacts', 'applicationContact')
            ->andWhere('application.id IN (:ids)')
            ->setParameter('ids', $applicationIds)
            ->orderBy('applicationContact.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationAttachments(null|array|Application $applications): void
    {
        if (empty($applications))
        {
            return;
        }

        $applicationIds = $this->getApplicationIds($applications);

        $this->createQueryBuilder('application')
            ->select('application, applicationAttachment')
            ->leftJoin('application.applicationAttachments', 'applicationAttachment')
            ->andWhere('application.id IN (:ids)')
            ->setParameter('ids', $applicationIds)
            ->orderBy('applicationAttachment.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationFormFieldValues(null|array|Application $applications): void
    {
        if (empty($applications))
        {
            return;
        }

        $applicationIds = $this->getApplicationIds($applications);

        $this->createQueryBuilder('application')
            ->select('application, applicationFormFieldValue')
            ->leftJoin('application.applicationFormFieldValues', 'applicationFormFieldValue')
            ->andWhere('application.id IN (:ids)')
            ->setParameter('ids', $applicationIds)
            ->orderBy('applicationFormFieldValue.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationPayments(null|array|Application $applications): void
    {
        if (empty($applications))
        {
            return;
        }

        $applicationIds = $this->getApplicationIds($applications);

        $this->createQueryBuilder('application')
            ->select('application, applicationPayment')
            ->leftJoin('application.applicationPayments', 'applicationPayment')
            ->andWhere('application.id IN (:ids)')
            ->setParameter('ids', $applicationIds)
            ->orderBy('applicationPayment.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationPurchasableItems(null|array|Application $applications): void
    {
        if (empty($applications))
        {
            return;
        }

        $applicationIds = $this->getApplicationIds($applications);

        /** @var Application[] $applications */
        $applications = $this->createQueryBuilder('application')
            ->select('application, applicationPurchasableItem, purchasableItem')
            ->leftJoin('application.applicationPurchasableItems', 'applicationPurchasableItem')
            ->leftJoin('applicationPurchasableItem.purchasableItem', 'purchasableItem')
            ->andWhere('application.id IN (:ids)')
            ->setParameter('ids', $applicationIds)
            ->orderBy('applicationPurchasableItem.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $applicationPurchasableItems = [];

        foreach ($applications as $application)
        {
            foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
            {
                $applicationPurchasableItems[] = $applicationPurchasableItem;
            }
        }

        $this->loadApplicationPurchasableItemInstances($applicationPurchasableItems);
    }

    private function loadApplicationAdminAttachments(null|array|Application $applications): void
    {
        if (empty($applications))
        {
            return;
        }

        $applicationIds = $this->getApplicationIds($applications);

        $this->createQueryBuilder('application')
            ->select('application, applicationAdminAttachment')
            ->leftJoin('application.applicationAdminAttachments', 'applicationAdminAttachment')
            ->andWhere('application.id IN (:ids)')
            ->setParameter('ids', $applicationIds)
            ->orderBy('applicationAdminAttachment.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationPurchasableItemInstances(null|array|ApplicationPurchasableItem $applicationPurchasableItems): void
    {
        if (empty($applicationPurchasableItems))
        {
            return;
        }

        $applicationPurchasableItemIds = $this->getApplicationPurchasableItemIds($applicationPurchasableItems);

        $this->_em->createQueryBuilder()
            ->select('applicationPurchasableItem, applicationPurchasableItemInstance')
            ->from(ApplicationPurchasableItem::class, 'applicationPurchasableItem')
            ->leftJoin('applicationPurchasableItem.applicationPurchasableItemInstances', 'applicationPurchasableItemInstance')
            ->leftJoin('applicationPurchasableItemInstance.applicationCamper', 'applicationCamper')
            ->andWhere('applicationPurchasableItem.id IN (:ids)')
            ->setParameter('ids', $applicationPurchasableItemIds)
            ->addOrderBy('applicationCamper.priority', 'DESC')
            ->addOrderBy('applicationPurchasableItemInstance.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    private function loadApplicationCampers(null|array|Application $applications): void
    {
        if (empty($applications))
        {
            return;
        }

        $applicationIds = $this->getApplicationIds($applications);

        /** @var Application[] $applications */
        $applications = $this->createQueryBuilder('application')
            ->select('application, applicationCamper')
            ->leftJoin('application.applicationCampers', 'applicationCamper')
            ->andWhere('application.id IN (:ids)')
            ->setParameter('ids', $applicationIds)
            ->orderBy('applicationCamper.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $applicationCampers = [];

        foreach ($applications as $application)
        {
            foreach ($application->getApplicationCampers() as $applicationCamper)
            {
                $applicationCampers[] = $applicationCamper;
            }
        }

        $this->loadApplicationCamperTripLocationPaths($applicationCampers);
        $this->loadApplicationCamperAttachments($applicationCampers);
        $this->loadApplicationCamperFormFieldValues($applicationCampers);
        $this->loadApplicationCamperPurchasableItemInstances($applicationCampers);
    }

    private function loadApplicationCamperTripLocationPaths(null|array|ApplicationCamper $applicationCampers): void
    {
        if (empty($applicationCampers))
        {
            return;
        }

        $applicationCamperIds = $this->getApplicationCamperIds($applicationCampers);

        $this->_em->createQueryBuilder()
            ->select('applicationCamper, applicationTripLocationPath')
            ->from(ApplicationCamper::class, 'applicationCamper')
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

        $this->_em->createQueryBuilder()
            ->select('applicationCamper, applicationFormFieldValue')
            ->from(ApplicationCamper::class, 'applicationCamper')
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

        $this->_em->createQueryBuilder()
            ->select('applicationCamper, applicationAttachment')
            ->from(ApplicationCamper::class, 'applicationCamper')
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

        $this->_em->createQueryBuilder()
            ->select('applicationCamper, applicationPurchasableItemInstance')
            ->from(ApplicationCamper::class, 'applicationCamper')
            ->leftJoin('applicationCamper.applicationPurchasableItemInstances', 'applicationPurchasableItemInstance')
            ->andWhere('applicationCamper.id IN (:ids)')
            ->setParameter('ids', $applicationCamperIds)
            ->orderBy('applicationPurchasableItemInstance.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    private function getApplicationIds(array|Application $applications): array
    {
        if ($applications instanceof Application)
        {
            $applications = [$applications];
        }

        return array_map(function (Application $application) {
            return $application->getId()->toBinary();
        }, $applications);
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

    private function getApplicationPurchasableItemIds(array|ApplicationPurchasableItem $applicationPurchasableItems): array
    {
        if ($applicationPurchasableItems instanceof ApplicationPurchasableItem)
        {
            $applicationPurchasableItems = [$applicationPurchasableItems];
        }

        return array_map(function (ApplicationPurchasableItem $applicationPurchasableItem) {
            return $applicationPurchasableItem->getId()->toBinary();
        }, $applicationPurchasableItems);
    }

    private function getSession(): SessionInterface
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        return $currentRequest->getSession();
    }

    private function getLocale(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request->getLocale();
    }
}