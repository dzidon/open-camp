<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\User;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

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
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_user_list')
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
        $this->addRoute($root, 'admin_user_list');
        $this->addRoute($root, 'admin_user_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(User $user): MenuType
    {
        $userId = $user->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_user_list');
        $this->addRoute($root, 'admin_user_read', ['id' => $userId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(User $user): MenuType
    {
        $userId = $user->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_user_list');
        $this->addRoute($root, 'admin_user_update', ['id' => $userId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdatePassword(User $user): MenuType
    {
        $userId = $user->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_user_list');
        $this->addRoute($root, 'admin_user_update', ['id' => $userId->toRfc4122()]);
        $this->addRoute($root, 'admin_user_update_password', ['id' => $userId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(User $user): MenuType
    {
        $userId = $user->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_user_list');
        $this->addRoute($root, 'admin_user_delete', ['id' => $userId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}