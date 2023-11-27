<?php

namespace App\Model\Service\Role;

use App\Model\Entity\Role;

/**
 * Initializes the "Super admin" role in the app.
 */
interface SuperAdminRoleInitializerInterface
{
    /**
     * Creates a new role named "Super admin" with all permissions if it doesn't exist already.
     * Re-assigns all permissions to a role named "Super admin" if it exists already.
     *
     * @return Role
     */
    public function initializeSuperAdminRole(): Role;
}