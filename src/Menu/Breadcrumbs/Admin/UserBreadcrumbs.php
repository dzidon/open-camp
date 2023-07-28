<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;
use Symfony\Component\Uid\UuidV4;

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
    public function buildRead(UuidV4 $userId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_read', ['id' => $userId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(UuidV4 $userId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_update', ['id' => $userId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdatePassword(UuidV4 $userId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_update', ['id' => $userId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_user_update_password', ['id' => $userId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(UuidV4 $userId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_user_list');
        $this->addChildRoute($root, 'admin_user_delete', ['id' => $userId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}