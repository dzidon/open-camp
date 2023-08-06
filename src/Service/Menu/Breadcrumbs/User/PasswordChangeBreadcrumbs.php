<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class PasswordChangeBreadcrumbs extends AbstractBreadcrumbs implements PasswordChangeBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildPasswordChange(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_login');
        $this->addChildRoute($root, 'user_password_change')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildPasswordChangeComplete(string $token): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_login');
        $this->addChildRoute($root, 'user_password_change');
        $this->addChildRoute($root, 'user_password_change_complete', ['token' => $token])
            ->setActive()
        ;

        return $root;
    }
}