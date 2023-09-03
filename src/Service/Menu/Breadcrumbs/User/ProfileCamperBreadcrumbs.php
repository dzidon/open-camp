<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Library\Menu\MenuType;
use App\Model\Entity\Camper;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

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
    public function buildRead(Camper $camper): MenuType
    {
        $camperId = $camper->getId();

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
    public function buildUpdate(Camper $camper): MenuType
    {
        $camperId = $camper->getId();

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
    public function buildDelete(Camper $camper): MenuType
    {
        $camperId = $camper->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'user_home');
        $this->addChildRoute($root, 'user_profile_camper_list');
        $this->addChildRoute($root, 'user_profile_camper_delete', ['id' => $camperId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}