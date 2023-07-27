<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;
use Symfony\Component\Uid\UuidV4;

/**
 * @inheritDoc
 */
class CampCategoryBreadcrumbs extends AbstractBreadcrumbs implements CampCategoryBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_category_list')
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
        $this->addChildRoute($root, 'admin_camp_category_list');
        $this->addChildRoute($root, 'admin_camp_category_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(UuidV4 $campCategoryId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_category_list');
        $this->addChildRoute($root, 'admin_camp_category_read', ['id' => $campCategoryId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(UuidV4 $campCategoryId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_category_list');
        $this->addChildRoute($root, 'admin_camp_category_update', ['id' => $campCategoryId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(UuidV4 $campCategoryId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_category_list');
        $this->addChildRoute($root, 'admin_camp_category_delete', ['id' => $campCategoryId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}