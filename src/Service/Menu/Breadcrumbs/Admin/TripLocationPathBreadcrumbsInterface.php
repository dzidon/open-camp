<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\TripLocationPathController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\TripLocationPath;

/**
 * Creates breadcrumbs for {@link TripLocationPathController}.
 */
interface TripLocationPathBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_trip_location_path_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_trip_location_path_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_trip_location_path_read".
     */
    public function buildRead(TripLocationPath $tripLocationPath): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_trip_location_path_update".
     */
    public function buildUpdate(TripLocationPath $tripLocationPath): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_trip_location_path_delete".
     */
    public function buildDelete(TripLocationPath $tripLocationPath): MenuTypeInterface;
}