<?php

namespace App\Menu\Breadcrumbs\User;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Type\MenuType;
use Symfony\Component\Uid\UuidV4;

/**
 * @inheritDoc
 */
class ProfileCamperBreadcrumbs extends AbstractBreadcrumbs implements ProfileCamperBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_camper_list')
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
        $this->addChildRoute($root, 'user_profile_camper_list');
        $this->addChildRoute($root, 'user_profile_camper_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(UuidV4 $camperId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_camper_list');
        $this->addChildRoute($root, 'user_profile_camper_read', ['id' => $camperId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(UuidV4 $camperId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_camper_list');
        $this->addChildRoute($root, 'user_profile_camper_update', ['id' => $camperId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(UuidV4 $camperId): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_camper_list');
        $this->addChildRoute($root, 'user_profile_camper_delete', ['id' => $camperId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}