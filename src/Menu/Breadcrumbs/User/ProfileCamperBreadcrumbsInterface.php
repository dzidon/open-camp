<?php

namespace App\Menu\Breadcrumbs\User;

use App\Controller\User\ProfileCamperController;
use App\Menu\Type\MenuTypeInterface;

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
    public function buildRead(int $camperId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_camper_update".
     */
    public function buildUpdate(int $camperId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_camper_delete".
     */
    public function buildDelete(int $camperId): MenuTypeInterface;
}