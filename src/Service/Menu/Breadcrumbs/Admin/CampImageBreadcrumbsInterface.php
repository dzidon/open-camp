<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\CampImageController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;

/**
 * Creates breadcrumbs for {@link CampImageController}.
 */
interface CampImageBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_camp_image_list".
     */
    public function buildList(Camp $camp): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_image_upload".
     */
    public function buildUpload(Camp $camp): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_image_update".
     */
    public function buildUpdate(Camp $camp, CampImage $campImage): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_image_delete".
     */
    public function buildDelete(Camp $camp, CampImage $campImage): MenuTypeInterface;
}