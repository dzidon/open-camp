<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\CampCategoryController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\CampCategory;

/**
 * Creates breadcrumbs for {@link CampCategoryController}.
 */
interface CampCategoryBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_camp_category_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_category_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_category_read".
     */
    public function buildRead(CampCategory $campCategory): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_category_update".
     */
    public function buildUpdate(CampCategory $campCategory): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_category_delete".
     */
    public function buildDelete(CampCategory $campCategory): MenuTypeInterface;
}