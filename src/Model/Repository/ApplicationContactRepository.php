<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationContact;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApplicationContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApplicationContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApplicationContact[]    findAll()
 * @method ApplicationContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationContactRepository extends AbstractRepository implements ApplicationContactRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationContact::class);
    }

    /**
     * @inheritDoc
     */
    public function saveApplicationContact(ApplicationContact $applicationContact, bool $flush): void
    {
        $this->save($applicationContact, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationContact(ApplicationContact $applicationContact, bool $flush): void
    {
        $this->remove($applicationContact, $flush);
    }
}