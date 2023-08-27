<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\CampDateController;
use App\Library\Menu\MenuTypeInterface;
use Symfony\Component\Uid\UuidV4;

/**
 * Creates breadcrumbs for {@link CampDateController}.
 */
interface CampDateBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_camp_date_list".
     */
    public function buildList(UuidV4 $campId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_date_upload".
     */
    public function buildCreate(UuidV4 $campId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_date_read".
     */
    public function buildRead(UuidV4 $campId, UuidV4 $campDateId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_date_update".
     */
    public function buildUpdate(UuidV4 $campId, UuidV4 $campDateId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_camp_date_delete".
     */
    public function buildDelete(UuidV4 $campId, UuidV4 $campDateId): MenuTypeInterface;
}