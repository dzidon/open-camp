<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\AttachmentConfigController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\AttachmentConfig;

/**
 * Creates breadcrumbs for {@link AttachmentConfigController}.
 */
interface AttachmentConfigBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_attachment_config_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_attachment_config_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_attachment_config_read".
     */
    public function buildRead(AttachmentConfig $attachmentConfig): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_attachment_config_update".
     */
    public function buildUpdate(AttachmentConfig $attachmentConfig): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_attachment_config_delete".
     */
    public function buildDelete(AttachmentConfig $attachmentConfig): MenuTypeInterface;
}