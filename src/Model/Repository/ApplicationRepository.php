<?php

namespace App\Model\Repository;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Library\Application\ApplicationsEditableDraftsResult;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
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

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(ManagerRegistry $registry,
                                RequestStack    $requestStack,
                                string          $lastCompletedApplicationIdSessionKey)
    {
        parent::__construct($registry, Application::class);

        $this->requestStack = $requestStack;
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
        $this->loadApplicationPurchasableItems($application);

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
        $this->loadApplicationPurchasableItems($application);

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
                application.id = otherApplicationCamper.application
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
            ->orderBy('applicationContact.createdAt', 'ASC')
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
            ->orderBy('applicationCamper.createdAt', 'ASC')
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
            ->andWhere('applicationPurchasableItem.id IN (:ids)')
            ->setParameter('ids', $applicationPurchasableItemIds)
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
}