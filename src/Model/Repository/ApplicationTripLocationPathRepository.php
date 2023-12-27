<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationTripLocationPath;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApplicationTripLocationPath|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationTripLocationPath|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationTripLocationPath[]    findAll()
 * @method ApplicationTripLocationPath[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationTripLocationPathRepository extends AbstractRepository implements ApplicationTripLocationPathRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationTripLocationPath::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationTripLocationPath(ApplicationTripLocationPath $applicationTripLocationPath, bool $flush): void
    {
        $this->save($applicationTripLocationPath, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationTripLocationPath(ApplicationTripLocationPath $applicationTripLocationPath, bool $flush): void
    {
        $this->remove($applicationTripLocationPath, $flush);
    }
}