<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;

/**
 * @inheritDoc
 */
class UserBreadcrumbs extends AbstractBreadcrumbs implements UserBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list')
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
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(int $userId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_read', ['id' => $userId])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(int $userId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_update', ['id' => $userId])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdatePassword(int $userId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_update', ['id' => $userId]);
        $this->addChildRoute($root, 'admin_user_update_password', ['id' => $userId])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(int $userId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_delete', ['id' => $userId])
            ->setActive()
        ;

        return $root;
    }
}