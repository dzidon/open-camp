<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;
use Symfony\Component\Uid\UuidV4;

/**
 * @inheritDoc
 */
class CampDateBreadcrumbs extends AbstractBreadcrumbs implements CampDateBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(UuidV4 $campId): MenuType
    {
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
    public function buildCreate(UuidV4 $campId): MenuType
    {
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
    public function buildRead(UuidV4 $campId, UuidV4 $campDateId): MenuType
    {
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
    public function buildUpdate(UuidV4 $campId, UuidV4 $campDateId): MenuType
    {
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
    public function buildDelete(UuidV4 $campId, UuidV4 $campDateId): MenuType
    {
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