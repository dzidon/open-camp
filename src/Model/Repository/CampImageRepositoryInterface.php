<?php

namespace App\Model\Repository;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\UuidV4;

/**
 * Camp image CRUD.
 */
interface CampImageRepositoryInterface
{
    /**
     * Saves a camp image.
     *
     * @param CampImage $campImage
     * @param bool $flush
     * @return void
     */
    public function saveCampImage(CampImage $campImage, bool $flush): void;

    /**
     * Removes a camp image.
     *
     * @param CampImage $campImage
     * @param bool $flush
     * @return void
     */
    public function removeCampImage(CampImage $campImage, bool $flush): void;

    /**
     * Creates a camp image.
     *
     * @param File $file
     * @param int $priority
     * @param Camp $camp
     * @return CampImage
     */
    public function createCampImage(File $file, int $priority, Camp $camp): CampImage;

    /**
     * Finds one camp image by id.
     *
     * @param UuidV4 $id
     * @return CampImage|null
     */
    public function findOneById(UuidV4 $id): ?CampImage;

    /**
     * Finds all camp images assigned to the given camp.
     *
     * @param Camp $camp
     * @return CampImage[]
     */
    public function findByCamp(Camp $camp): array;

    /**
     * Returns admin camp search paginator.
     *
     * @param Camp $camp
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(Camp $camp, int $currentPage, int $pageSize): PaginatorInterface;
}