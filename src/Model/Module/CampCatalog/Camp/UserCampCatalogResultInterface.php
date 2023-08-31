<?php

namespace App\Model\Module\CampCatalog\Camp;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;

/**
 * Data class for camp catalog.
 */
interface UserCampCatalogResultInterface
{
    /**
     * Returns the camp paginator.
     *
     * @return PaginatorInterface
     */
    public function getPaginator(): PaginatorInterface;

    /**
     * Returns the main image of the given camp.
     *
     * @param string|Camp $camp
     * @return CampImage|null
     */
    public function getCampImage(string|Camp $camp): ?CampImage;

    /**
     * Returns available camp images of the given camp.
     *
     * @param string|Camp $camp
     * @return array
     */
    public function getCampDates(string|Camp $camp): array;
}