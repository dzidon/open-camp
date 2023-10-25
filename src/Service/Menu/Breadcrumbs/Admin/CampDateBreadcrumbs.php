<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class CampDateBreadcrumbs extends AbstractBreadcrumbs implements CampDateBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(Camp $camp): MenuType
    {
        $campId = $camp->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_list', ['id' => $campId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildCreate(Camp $camp): MenuType
    {
        $campId = $camp->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_list', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_create', ['id' => $campId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(CampDate $campDate): MenuType
    {
        $camp = $campDate->getCamp();
        $campId = $camp->getId();
        $campDateId = $campDate->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_list', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_read', ['id' => $campDateId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(CampDate $campDate): MenuType
    {
        $camp = $campDate->getCamp();
        $campId = $camp->getId();
        $campDateId = $campDate->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_list', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_update', ['id' => $campDateId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(CampDate $campDate): MenuType
    {
        $camp = $campDate->getCamp();
        $campId = $camp->getId();
        $campDateId = $campDate->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_list', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_date_delete', ['id' => $campDateId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}