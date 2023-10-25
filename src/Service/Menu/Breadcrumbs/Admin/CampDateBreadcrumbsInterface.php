<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\CampDateController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;

/**
 * Creates breadcrumbs for {@link CampDateController}.
 */
interface CampDateBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_camp_date_list".
     */
    public function buildList(Camp $camp): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_date_upload".
     */
    public function buildCreate(Camp $camp): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_date_read".
     */
    public function buildRead(CampDate $campDate): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_date_update".
     */
    public function buildUpdate(CampDate $campDate): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_date_delete".
     */
    public function buildDelete(CampDate $campDate): MenuTypeInterface;
}