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

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_image_list', ['id' => $campId->toRfc4122()])
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

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_image_list', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_image_upload', ['id' => $campId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(Camp $camp, CampImage $campImage): MenuTypeInterface
    {
        $campId = $camp->getId();
        $campImageId = $campImage->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_image_list', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_image_update', ['id' => $campImageId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(Camp $camp, CampImage $campImage): MenuTypeInterface
    {
        $campId = $camp->getId();
        $campImageId = $campImage->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_camp_list');
        $this->addChildRoute($root, 'admin_camp_update', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_image_list', ['id' => $campId->toRfc4122()]);
        $this->addChildRoute($root, 'admin_camp_image_delete', ['id' => $campImageId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}