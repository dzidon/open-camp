<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;
use Symfony\Component\Uid\UuidV4;

/**
 * @inheritDoc
 */
class CampImageBreadcrumbs extends AbstractBreadcrumbs implements CampImageBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(UuidV4 $campId): MenuType
    {
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
    public function buildUpload(UuidV4 $campId): MenuType
    {
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
    public function buildUpdate(UuidV4 $campId, UuidV4 $campImageId): MenuTypeInterface
    {
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
    public function buildDelete(UuidV4 $campId, UuidV4 $campImageId): MenuTypeInterface
    {
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