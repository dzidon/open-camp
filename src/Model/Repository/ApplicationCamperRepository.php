<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationCamper;
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
}