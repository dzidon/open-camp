<?php

namespace App\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;
use Symfony\Component\Uid\UuidV4;

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
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_contact_list')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildCreate(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_contact_list');
        $this->addChildRoute($root, 'user_profile_contact_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(UuidV4 $contactId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_contact_list');
        $this->addChildRoute($root, 'user_profile_contact_read', ['id' => $contactId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(UuidV4 $contactId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_contact_list');
        $this->addChildRoute($root, 'user_profile_contact_update', ['id' => $contactId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(UuidV4 $contactId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_contact_list');
        $this->addChildRoute($root, 'user_profile_contact_delete', ['id' => $contactId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}