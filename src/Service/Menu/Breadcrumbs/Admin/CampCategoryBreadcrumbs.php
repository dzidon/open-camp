<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\CampCategory;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

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
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_camp_category_list')
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
        $this->addRoute($root, 'admin_camp_category_list');
        $this->addRoute($root, 'admin_camp_category_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(CampCategory $campCategory): MenuType
    {
        $campCategoryId = $campCategory->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_camp_category_list');
        $this->addRoute($root, 'admin_camp_category_read', ['id' => $campCategoryId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(CampCategory $campCategory): MenuType
    {
        $campCategoryId = $campCategory->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_camp_category_list');
        $this->addRoute($root, 'admin_camp_category_update', ['id' => $campCategoryId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(CampCategory $campCategory): MenuType
    {
        $campCategoryId = $campCategory->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_camp_category_list');
        $this->addRoute($root, 'admin_camp_category_delete', ['id' => $campCategoryId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}