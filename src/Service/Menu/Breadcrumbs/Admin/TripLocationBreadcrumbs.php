<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class TripLocationBreadcrumbs extends AbstractBreadcrumbs implements TripLocationBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildCreate(TripLocationPath $tripLocationPath): MenuType
    {
        $tripLocationPathId = $tripLocationPath->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_trip_location_path_list');
        $this->addChildRoute($root, 'admin_trip_location_path_update', ['id' => $tripLocationPathId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_trip_location_create', ['id' => $tripLocationPathId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(TripLocationPath $tripLocationPath, TripLocation $tripLocation): MenuType
    {
        $tripLocationPathId = $tripLocationPath->getId();
        $tripLocationId = $tripLocation->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_trip_location_path_list');
        $this->addChildRoute($root, 'admin_trip_location_path_update', ['id' => $tripLocationPathId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_trip_location_read', ['id' => $tripLocationId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(TripLocationPath $tripLocationPath, TripLocation $tripLocation): MenuType
    {
        $tripLocationPathId = $tripLocationPath->getId();
        $tripLocationId = $tripLocation->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_trip_location_path_list');
        $this->addChildRoute($root, 'admin_trip_location_path_update', ['id' => $tripLocationPathId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_trip_location_update', ['id' => $tripLocationId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(TripLocationPath $tripLocationPath, TripLocation $tripLocation): MenuType
    {
        $tripLocationPathId = $tripLocationPath->getId();
        $tripLocationId = $tripLocation->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_trip_location_path_list');
        $this->addChildRoute($root, 'admin_trip_location_path_update', ['id' => $tripLocationPathId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_trip_location_delete', ['id' => $tripLocationId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}