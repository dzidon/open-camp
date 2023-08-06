<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\RegistrationController;
use App\Library\Menu\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link RegistrationController}.
 */
interface RegistrationBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_registration".
     */
    public function buildRegistration(): MenuTypeInterface;

    /**
     * Initializes breadcrumbs for the path "user_registration_complete".
     */
    public function buildRegistrationComplete(string $token): MenuTypeInterface;
}