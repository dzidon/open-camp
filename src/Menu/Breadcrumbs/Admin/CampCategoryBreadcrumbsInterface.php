<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\CampCategoryController;
use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\Uid\UuidV4;

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
    public function buildRead(UuidV4 $campCategoryId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_category_update".
     */
    public function buildUpdate(UuidV4 $campCategoryId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_category_delete".
     */
    public function buildDelete(UuidV4 $campCategoryId): MenuTypeInterface;
}