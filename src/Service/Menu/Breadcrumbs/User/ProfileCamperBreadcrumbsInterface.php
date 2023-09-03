<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\ProfileCamperController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camper;

/**
 * Creates breadcrumbs for {@link ProfileCamperController}.
 */
interface ProfileCamperBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "user_profile_camper_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_camper_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_camper_read".
     */
    public function buildRead(Camper $camper): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_camper_update".
     */
    public function buildUpdate(Camper $camper): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_camper_delete".
     */
    public function buildDelete(Camper $camper): MenuTypeInterface;
}