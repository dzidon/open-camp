<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\TextContentData;
use App\Model\Entity\TextContent;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link TextContentData} to {@link TextContent} and vice versa.
 */
class TextContentDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof TextContentData && $entity instanceof TextContent;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var TextContentData $textContentData */
        /** @var TextContent $textContent */
        $textContentData = $data;
        $textContent = $entity;

        $textContentData->setContent($textContent->getContent());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var TextContentData $textContentData */
        /** @var TextContent $textContent */
        $textContentData = $data;
        $textContent = $entity;

        $textContent->setContent($textContentData->getContent());
    }
}