<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

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
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_registration')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRegistrationComplete(string $token): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_registration');
        $this->addRoute($root, 'user_registration_complete', ['token' => $token])
            ->setActive()
        ;

        return $root;
    }
}