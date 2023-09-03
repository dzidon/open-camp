<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\ProfileContactController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Contact;

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
    public function buildRead(Contact $contact): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_contact_update".
     */
    public function buildUpdate(Contact $contact): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "user_profile_contact_delete".
     */
    public function buildDelete(Contact $contact): MenuTypeInterface;
}