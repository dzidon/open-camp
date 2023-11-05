<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Model\Entity\CampDateAttachmentConfig;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link CampDateAttachmentConfigData} to {@link CampDateAttachmentConfig} and vice versa.
 */
class CampDateAttachmentConfigDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CampDateAttachmentConfigData && $entity instanceof CampDateAttachmentConfig;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CampDateAttachmentConfigData $campDateAttachmentConfigData */
        /** @var CampDateAttachmentConfig $campDateAttachmentConfig */
        $campDateAttachmentConfigData = $data;
        $campDateAttachmentConfig = $entity;

        $campDateAttachmentConfigData->setAttachmentConfig($campDateAttachmentConfig->getAttachmentConfig());
        $campDateAttachmentConfigData->setPriority($campDateAttachmentConfig->getPriority());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CampDateAttachmentConfigData $campDateAttachmentConfigData */
        /** @var CampDateAttachmentConfig $campDateAttachmentConfig */
        $campDateAttachmentConfigData = $data;
        $campDateAttachmentConfig = $entity;

        $campDateAttachmentConfig->setAttachmentConfig($campDateAttachmentConfigData->getAttachmentConfig());
        $campDateAttachmentConfig->setPriority($campDateAttachmentConfigData->getPriority());
    }
}