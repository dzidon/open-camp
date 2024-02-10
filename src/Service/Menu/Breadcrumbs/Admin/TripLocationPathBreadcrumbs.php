<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\TripLocationPath;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class TripLocationPathBreadcrumbs extends AbstractBreadcrumbs implements TripLocationPathBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_trip_location_path_list')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildCreate(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_trip_location_path_list');
        $this->addRoute($root, 'admin_trip_location_path_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(TripLocationPath $tripLocationPath): MenuType
    {
        $tripLocationPathId = $tripLocationPath->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_trip_location_path_list');
        $this->addRoute($root, 'admin_trip_location_path_read', ['id' => $tripLocationPathId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(TripLocationPath $tripLocationPath): MenuType
    {
        $tripLocationPathId = $tripLocationPath->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_trip_location_path_list');
        $this->addRoute($root, 'admin_trip_location_path_update', ['id' => $tripLocationPathId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(TripLocationPath $tripLocationPath): MenuType
    {
        $tripLocationPathId = $tripLocationPath->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_trip_location_path_list');
        $this->addRoute($root, 'admin_trip_location_path_delete', ['id' => $tripLocationPathId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}