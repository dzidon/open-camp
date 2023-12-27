<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationCamper;
use Doctrine\Persistence\ManagerRegistry;

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
}