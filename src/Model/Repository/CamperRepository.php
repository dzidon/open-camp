<?php

namespace App\Model\Repository;

use App\Enum\GenderEnum;
use App\Form\DataTransfer\Data\User\CamperSearchDataInterface;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Search\Paginator\DqlPaginator;
use DateTimeImmutable;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

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
    public function createCamper(string $name, GenderEnum $gender, DateTimeImmutable $bornAt, User $user): Camper
    {
        return new Camper($name, $gender, $bornAt, $user);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(int $id): ?Camper
    {
        return $this->createQueryBuilder('camper')
            ->select('camper, camperUser')
            ->leftJoin('camper.user', 'camperUser')
            ->andWhere('camper.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getUserPaginator(CamperSearchDataInterface $data, User $user, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('camper')
            ->andWhere('camper.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->andWhere('camper.user = :user')
            ->setParameter('user', $user)
            ->orderBy('camper.' . $sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query), $currentPage, $pageSize);
    }
}