<?php

namespace App\Model\Repository;

use App\Library\Search\Paginator\DqlPaginator;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Service\CampImage\CampImageFilesystemInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\UuidV4;

/**
 * @method CampImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampImage[]    findAll()
 * @method CampImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampImageRepository extends AbstractRepository implements CampImageRepositoryInterface
{
    private CampImageFilesystemInterface $campImageFilesystem;

    public function __construct(ManagerRegistry $registry, CampImageFilesystemInterface $campImageFilesystem)
    {
        parent::__construct($registry, CampImage::class);

        $this->campImageFilesystem = $campImageFilesystem;
    }

    /**
     * @inheritDoc
     */
    public function saveCampImage(CampImage $campImage, bool $flush): void
    {
        $this->save($campImage, $flush);
    }

    /**
     * @inheritDoc
     */
    public function removeCampImage(CampImage $campImage, bool $flush): void
    {
        $this->campImageFilesystem->removeFile($campImage);
        $this->remove($campImage, $flush);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(UuidV4 $id): ?CampImage
    {
        return $this->createQueryBuilder('campImage')
            ->select('campImage, camp')
            ->leftJoin('campImage.camp', 'camp')
            ->andWhere('campImage.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function findByCamp(Camp $camp): array
    {
        return $this->createQueryBuilder('campImage')
            ->andWhere('campImage.camp = :campId')
            ->setParameter('campId', $camp->getId(), UuidType::NAME)
            ->orderBy('campImage.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getAdminPaginator(Camp $camp, int $currentPage, int $pageSize): DqlPaginator
    {
        $query = $this->createQueryBuilder('campImage')
            ->andWhere('campImage.camp = :campId')
            ->setParameter('campId', $camp->getId(), UuidType::NAME)
            ->orderBy('campImage.priority', 'DESC')
            ->getQuery()
        ;

        return new DqlPaginator(new DoctrinePaginator($query, false), $currentPage, $pageSize);
    }
}