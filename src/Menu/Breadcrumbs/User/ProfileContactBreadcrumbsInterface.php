<?php

namespace App\Menu\Breadcrumbs\User;

use App\Controller\User\ProfileContactController;
use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\Uid\UuidV4;

/**
 * Creates breadcrumbs for {@link ProfileContactController}.
 */
interface ProfileContactBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "user_profile_contact_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_contact_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_contact_read".
     */
    public function buildRead(UuidV4 $contactId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_contact_update".
     */
    public function buildUpdate(UuidV4 $contactId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_contact_delete".
     */
    public function buildDelete(UuidV4 $contactId): MenuTypeInterface;
}