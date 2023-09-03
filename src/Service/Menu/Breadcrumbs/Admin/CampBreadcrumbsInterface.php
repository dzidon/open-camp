<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\CampController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camp;

/**
 * Creates breadcrumbs for {@link CampController}.
 */
interface CampBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_camp_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_read".
     */
    public function buildRead(Camp $camp): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_update".
     */
    public function buildUpdate(Camp $camp): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_delete".
     */
    public function buildDelete(Camp $camp): MenuTypeInterface;
}