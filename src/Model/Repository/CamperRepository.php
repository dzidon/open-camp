<?php

namespace App\Model\Repository;

use App\Library\Data\User\CamperSearchData;
use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method Camper|null find($id, $lockMode = null, $lockVersion = null)
 * @method Camper|null findOneBy(array $criteria, array $orderBy = null)
 * @method Camper[]    findAll()
 * @method Camper[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CamperRepository extends AbstractRepository implements CamperRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Camper::class);
    }

    /**
     * @inheritDoc
     */
    public function saveCamper(Camper $camper, bool $flush): void
    {
        $this->save($camper, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCamper(Camper $camper, bool $flush): void
    {
        $this->remove($camper, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?Camper
    {
        return $this->createQueryBuilder('camper')
            ->select('camper, camperUser')
            ->leftJoin('camper.user', 'camperUser')
            ->andWhere('camper.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('camper')
            ->andWhere('camper.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findOwnedBySameUser(Camper $camper): array
    {
        $user = $camper->getUser();

        return $this->createQueryBuilder('camper')
            ->andWhere('camper.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->andWhere('camper.id != :id')
            ->setParameter('id', $camper->getId(), UuidType::NAME)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getUserPaginator(CamperSearchData $data, User $user, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('camper')
            ->andWhere('CONCAT(camper.nameFirst, \' \', camper.nameLast) LIKE :fullName')
            ->setParameter('fullName', '%' . $phrase . '%')
            ->andWhere('camper.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}