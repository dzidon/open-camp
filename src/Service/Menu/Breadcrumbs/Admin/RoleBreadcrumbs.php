<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\Role;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class RoleBreadcrumbs extends AbstractBreadcrumbs implements RoleBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_role_list')
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
        $this->addRoute($root, 'admin_role_list');
        $this->addRoute($root, 'admin_role_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(Role $role): MenuType
    {
        $roleId = $role->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_role_list');
        $this->addRoute($root, 'admin_role_read', ['id' => $roleId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(Role $role): MenuType
    {
        $roleId = $role->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_role_list');
        $this->addRoute($root, 'admin_role_update', ['id' => $roleId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(Role $role): MenuType
    {
        $roleId = $role->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_role_list');
        $this->addRoute($root, 'admin_role_delete', ['id' => $roleId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}