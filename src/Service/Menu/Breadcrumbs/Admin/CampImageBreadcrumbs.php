<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class CampImageBreadcrumbs extends AbstractBreadcrumbs implements CampImageBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(Camp $camp): MenuType
    {
        $campId = $camp->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_camp_list');
        $this->addRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addRoute($root, 'admin_camp_image_list', ['id' => $campId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpload(Camp $camp): MenuType
    {
        $campId = $camp->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_camp_list');
        $this->addRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addRoute($root, 'admin_camp_image_list', ['id' => $campId->toRfc4122()]);
        $this->addRoute($root, 'admin_camp_image_upload', ['id' => $campId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(CampImage $campImage): MenuTypeInterface
    {
        $camp = $campImage->getCamp();
        $campId = $camp->getId();
        $campImageId = $campImage->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_camp_list');
        $this->addRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addRoute($root, 'admin_camp_image_list', ['id' => $campId->toRfc4122()]);
        $this->addRoute($root, 'admin_camp_image_update', ['id' => $campImageId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(CampImage $campImage): MenuTypeInterface
    {
        $camp = $campImage->getCamp();
        $campId = $camp->getId();
        $campImageId = $campImage->getId();

        $root = $this->createBreadcrumbs();
        $this->addRoute($root, 'admin_home');
        $this->addRoute($root, 'admin_camp_list');
        $this->addRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addRoute($root, 'admin_camp_image_list', ['id' => $campId->toRfc4122()]);
        $this->addRoute($root, 'admin_camp_image_delete', ['id' => $campImageId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}