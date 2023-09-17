<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Library\Menu\MenuType;
use App\Model\Entity\AttachmentConfig;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumbs;

/**
 * @inheritDoc
 */
class AttachmentConfigBreadcrumbs extends AbstractBreadcrumbs implements AttachmentConfigBreadcrumbsInterface
{
    /**
     * @inheritDoc
     */
    public function buildList(): MenuType
    {
        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_attachment_config_list')
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
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_attachment_config_list');
        $this->addChildRoute($root, 'admin_attachment_config_create')
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildRead(AttachmentConfig $attachmentConfig): MenuType
    {
        $attachmentConfigId = $attachmentConfig->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_attachment_config_list');
        $this->addChildRoute($root, 'admin_attachment_config_read', ['id' => $attachmentConfigId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildUpdate(AttachmentConfig $attachmentConfig): MenuType
    {
        $attachmentConfigId = $attachmentConfig->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_attachment_config_list');
        $this->addChildRoute($root, 'admin_attachment_config_update', ['id' => $attachmentConfigId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }

    /**
     * @inheritDoc
     */
    public function buildDelete(AttachmentConfig $attachmentConfig): MenuType
    {
        $attachmentConfigId = $attachmentConfig->getId();

        $root = $this->createRoot();
        $this->addChildRoute($root, 'admin_home');
        $this->addChildRoute($root, 'admin_attachment_config_list');
        $this->addChildRoute($root, 'admin_attachment_config_delete', ['id' => $attachmentConfigId->toRfc4122()])
            ->setActive()
        ;

        return $root;
    }
}