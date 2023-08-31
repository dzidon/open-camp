<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\CampCatalogController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampCategory;

/**
 * Creates breadcrumbs for {@link CampCatalogController}.
 */
interface CampCatalogBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "user_camp_catalog".
     *
     * @param CampCategory[] $campCategories Camp categories or paths
     * @return MenuTypeInterface
     */
    public function buildList(array $campCategories): MenuTypeInterface;
}