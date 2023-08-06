<?php

namespace App\Tests\Service\Data\Transfer;

use App\Service\Data\Transfer\DataTransferInterface;
use App\Tests\Library\Data\LabelDataMock;
use App\Tests\Model\Entity\EntityMock;

class LabelDataTransferMock implements DataTransferInterface
{
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof LabelDataMock && $entity instanceof EntityMock;
    }

    public function fillData(object $data, object $entity): void
    {
        /** @var LabelDataMock $labelData */
        /** @var EntityMock $entityMock */
        $labelData = $data;
        $entityMock = $entity;

        $labelData->setLabel($entityMock->getLabel());
    }

    public function fillEntity(object $data, object $entity): void
    {
        /** @var LabelDataMock $labelData */
        /** @var EntityMock $entityMock */
        $labelData = $data;
        $entityMock = $entity;

        $entityMock->setLabel($labelData->getLabel());
    }
}