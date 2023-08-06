<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\CampController;
use App\Library\Menu\MenuTypeInterface;
use Symfony\Component\Uid\UuidV4;

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
    public function buildRead(UuidV4 $campId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_update".
     */
    public function buildUpdate(UuidV4 $campId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_delete".
     */
    public function buildDelete(UuidV4 $campId): MenuTypeInterface;
}