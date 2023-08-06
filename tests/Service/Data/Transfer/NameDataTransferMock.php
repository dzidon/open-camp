<?php

namespace App\Tests\Service\Data\Transfer;

use App\Service\Data\Transfer\DataTransferInterface;
use App\Tests\Library\Data\NameDataMock;
use App\Tests\Model\Entity\EntityMock;

class NameDataTransferMock implements DataTransferInterface
{
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof NameDataMock && $entity instanceof EntityMock;
    }

    public function fillData(object $data, object $entity): void
    {
        /** @var NameDataMock $nameData */
        /** @var EntityMock $entityMock */
        $nameData = $data;
        $entityMock = $entity;

        $nameData->setName($entityMock->getName());
    }

    public function fillEntity(object $data, object $entity): void
    {
        /** @var NameDataMock $nameData */
        /** @var EntityMock $entityMock */
        $nameData = $data;
        $entityMock = $entity;

        $entityMock->setName($nameData->getName());
    }
}