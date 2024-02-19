<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationCamper;
use App\Model\Entity\CampDate;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

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