<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\TripLocationController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;

/**
 * Creates breadcrumbs for {@link TripLocationController}.
 */
interface TripLocationBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_trip_location_create".
     */
    public function buildCreate(TripLocationPath $tripLocationPath): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_trip_location_read".
     */
    public function buildRead(TripLocationPath $tripLocationPath, TripLocation $tripLocation): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_trip_location_update".
     */
    public function buildUpdate(TripLocationPath $tripLocationPath, TripLocation $tripLocation): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_trip_location_delete".
     */
    public function buildDelete(TripLocationPath $tripLocationPath, TripLocation $tripLocation): MenuTypeInterface;
}