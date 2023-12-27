<?php

namespace App\Model\Repository;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationCamper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends AbstractRepository implements ApplicationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
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
            ->select('application, campDate, user, camp, leader')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('application.user', 'user')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('campDate.leaders', 'leader')
            ->andWhere('application.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->loadApplicationContacts($application);
        $this->loadApplicationCampers($application);
        $this->loadApplicationAttachments($application);
        $this->loadApplicationFormFieldValues($application);

        return $application;
    }

    /**
     * @inheritDoc
     */
    public function findOneBySimpleId(string $simpleId): ?Application
    {
        /** @var Application|null $application */
        $application = $this->createQueryBuilder('application')
            ->select('application, campDate, user, camp, leader')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('application.user', 'user')
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
}