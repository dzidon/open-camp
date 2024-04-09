<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ApplicationAdminAttachmentUpdateData;
use App\Model\Entity\ApplicationAdminAttachment;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ApplicationAdminAttachmentUpdateData} to {@link ApplicationAdminAttachment} and vice versa.
 */
class ApplicationAdminAttachmentUpdateDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationAdminAttachmentUpdateData && $entity instanceof ApplicationAdminAttachment;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationAdminAttachmentUpdateData $applicationAdminAttachmentUpdateData */
        /** @var ApplicationAdminAttachment $applicationAdminAttachment */
        $applicationAdminAttachmentUpdateData = $data;
        $applicationAdminAttachment = $entity;

        $applicationAdminAttachmentUpdateData->setLabel($applicationAdminAttachment->getLabel());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationAdminAttachmentUpdateData $applicationAdminAttachmentUpdateData */
        /** @var ApplicationAdminAttachment $applicationAdminAttachment */
        $applicationAdminAttachmentUpdateData = $data;
        $applicationAdminAttachment = $entity;

        $applicationAdminAttachment->setLabel($applicationAdminAttachmentUpdateData->getLabel());
    }
}