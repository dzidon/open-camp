<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationContactSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\ApplicationContact;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

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

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?ApplicationContact
    {
        return $this->createQueryBuilder('applicationContact')
            ->select('applicationContact, application, applicationContactOther')
            ->leftJoin('applicationContact.application', 'application')
            ->leftJoin('application.campDate', 'campDate')
            ->leftJoin('campDate.camp', 'camp')
            ->leftJoin('application.applicationContacts', 'applicationContactOther')
            ->andWhere('applicationContact.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(ApplicationContactSearchData $data, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('applicationContact')
            ->andWhere('CONCAT(applicationContact.nameFirst, \' \', applicationContact.nameLast) LIKE :phrase')
            ->setParameter('phrase', '%' . $phrase . '%')
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}