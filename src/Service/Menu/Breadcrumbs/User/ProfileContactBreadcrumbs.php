<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Model\Entity\Contact;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class ProfileContactBreadcrumbs extends AbstractBreadcrumbs implements ProfileContactBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_profile_contact_list')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildCreate(): MenuType
    {
        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_profile_contact_list');
        $this->addRoute($root, 'user_profile_contact_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(Contact $contact): MenuType
    {
        $contactId = $contact->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_profile_contact_list');
        $this->addRoute($root, 'user_profile_contact_read', ['id' => $contactId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(Contact $contact): MenuType
    {
        $contactId = $contact->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_profile_contact_list');
        $this->addRoute($root, 'user_profile_contact_update', ['id' => $contactId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(Contact $contact): MenuType
    {
        $contactId = $contact->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'user_home');
        $this->addRoute($root, 'user_profile_contact_list');
        $this->addRoute($root, 'user_profile_contact_delete', ['id' => $contactId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}