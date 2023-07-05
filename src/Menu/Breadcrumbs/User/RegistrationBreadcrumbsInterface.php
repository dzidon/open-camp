<?php

namespace App\Menu\Breadcrumbs\User;

use App\Controller\User\RegistrationController;
use App\Menu\Type\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link RegistrationController}.
 */
interface RegistrationBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_registration".
     *
     * @return MenuTypeInterface
     */
    public function buildRegistration(): MenuTypeInterface;

    /**
     * Initializes breadcrumbs for the path "user_registration_complete".
     *
     * @param string $token
     * @return MenuTypeInterface
     */
    public function buildRegistrationComplete(string $token): MenuTypeInterface;
}