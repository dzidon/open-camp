<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\CampImageController;
use App\Library\Menu\MenuTypeInterface;
use Symfony\Component\Uid\UuidV4;

/**
 * Creates breadcrumbs for {@link CampImageController}.
 */
interface CampImageBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_camp_image_list".
     */
    public function buildList(UuidV4 $campId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_image_upload".
     */
    public function buildUpload(UuidV4 $campId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_image_update".
     */
    public function buildUpdate(UuidV4 $campId, UuidV4 $campImageId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_image_delete".
     */
    public function buildDelete(UuidV4 $campId, UuidV4 $campImageId): MenuTypeInterface;
}