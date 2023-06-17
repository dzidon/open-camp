<?php

namespace App\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;

/**
 * @inheritDoc
 */
class RegistrationBreadcrumbs extends AbstractBreadcrumbs implements RegistrationBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildRegistration(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_registration')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRegistrationComplete(string $token): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_registration');
        $this->addChildRoute($root, 'user_registration_complete', ['token' => $token])
            ->setActive()
        ;

        return $root;
    }
}