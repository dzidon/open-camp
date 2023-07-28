<?php

namespace App\Menu\Breadcrumbs\User;

use App\Controller\User\ProfileCamperController;
use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\Uid\UuidV4;

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
    public function buildRead(UuidV4 $camperId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_camper_update".
     */
    public function buildUpdate(UuidV4 $camperId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_camper_delete".
     */
    public function buildDelete(UuidV4 $camperId): MenuTypeInterface;
}