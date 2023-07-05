<?php

namespace App\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;

/**
 * @inheritDoc
 */
class ProfileBreadcrumbs extends AbstractBreadcrumbs implements ProfileBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildPasswordChange(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_password_change')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildPasswordChangeCreate(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_password_change_create')
            ->setActive()
        ;

        return $root;
    }
}