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
    public function createCamper(string $name, GenderEnum $gender, DateTimeImmutable $bornAt, User $user): Camper
    {
        return new Camper($name, $gender, $bornAt, $user);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?Camper
    {
        return $this->createQueryBuilder('camper')
            ->select('camper, camperUser, camperSiblings')
            ->leftJoin('camper.user', 'camperUser')
            ->leftJoin('camper.siblings', 'camperSiblings')
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
            ->select('camper, camperSiblings')
            ->leftJoin('camper.siblings', 'camperSiblings')
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
            ->select('camper, camperSiblings')
            ->leftJoin('camper.siblings', 'camperSiblings')
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
    public function getUserPaginator(CamperSearchDataInterface $data, User $user, int $currentPage, int $pageSize): DqlPaginator
    {
        $phrase = $data->getPhrase();
        $sortBy = $data->getSortBy();

        $query = $this->createQueryBuilder('camper')
            ->andWhere('camper.name LIKE :name')
            ->setParameter('name', '%' . $phrase . '%')
            ->andWhere('camper.user = :userId')
            ->setParameter('userId', $user->getId(), UuidType::NAME)
            ->orderBy($sortBy->property(), $sortBy->order())
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}