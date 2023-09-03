<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\Camp;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class CampBreadcrumbs extends AbstractBreadcrumbs implements CampBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildCreate(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(Camp $camp): MenuType
    {
        $campId = $camp->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_read', ['id' => $campId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(Camp $camp): MenuType
    {
        $campId = $camp->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(Camp $camp): MenuType
    {
        $campId = $camp->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_delete', ['id' => $campId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}